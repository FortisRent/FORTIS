package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.ServiceOrderEmployeeModel;
import org.fortis.service.Db;
import org.fortis.service.ResponseMessage;

import jakarta.ws.rs.Consumes;
import jakarta.ws.rs.DELETE;
import jakarta.ws.rs.GET;
import jakarta.ws.rs.POST;
import jakarta.ws.rs.PUT;
import jakarta.ws.rs.Path;
import jakarta.ws.rs.PathParam;
import jakarta.ws.rs.Produces;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;

// {
//     "service_order_id": 1,
//     "employee_id": 1
// }

@Path("/v1/so/employee")
public class ServiceOrderEmployeeResource {

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getEmployees() {
		List<ServiceOrderEmployeeModel> service_order_employee_list = new ArrayList<>();
		String query = "SELECT * FROM service_order_employee WHERE deleted_at IS NULL";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				ServiceOrderEmployeeModel service_order_employee = new ServiceOrderEmployeeModel();
				service_order_employee.setId			        (rs.getInt		("id"));
				service_order_employee.setUuid		            (rs.getString	("uuid"));
				service_order_employee.setService_order_id      (rs.getInt		("service_order_id"));
				service_order_employee.setEmployee_id           (rs.getInt	    ("employee_id"));
				service_order_employee.setCreated_at	        (rs.getString	("created_at"));
				service_order_employee_list.add(service_order_employee);
			}

			return Response.ok(service_order_employee_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar funcionários.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createEmployee(ServiceOrderEmployeeModel newEmployee) {
		String query = "INSERT INTO service_order_employee (uuid, service_order_id, employee_id) VALUES (?, ?, ?)";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString	(1,	UUID.randomUUID().toString());
			stmt.setInt		(2, 	newEmployee.getService_order_id());
			stmt.setInt		(3, 	newEmployee.getEmployee_id());
			stmt.executeUpdate();

			ResponseMessage responseMessage = new ResponseMessage("Funcionário cadastrado.");
			return Response.ok(responseMessage).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro ao cadastrar funcionário.").build();
		}
	}

	@GET
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response getEmployeeById(@PathParam("uuid") String uuid) {
		String query = "SELECT * FROM service_order_employee WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					ServiceOrderEmployeeModel service_order_employee = new ServiceOrderEmployeeModel();
					service_order_employee.setId(rs.getInt("id"));
					service_order_employee.setUuid(rs.getString("uuid"));
					service_order_employee.setService_order_id(rs.getInt("service_order_id"));
					service_order_employee.setEmployee_id(rs.getInt("employee_id"));
					service_order_employee.setCreated_at(rs.getString("created_at"));
					return Response.ok(service_order_employee).build();
				} else {
					return Response.status(404).entity("Funcionário não encontrado.").build();
				}
			}
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Erro ao encontrar funcionário.").build();
		}
	}

	@PUT
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public Response updateEmployee(
		@PathParam("uuid") String uuid,
		ServiceOrderEmployeeModel updatedEmployee) {
		String query = "UPDATE service_order_employee SET service_order_id = ?, emplyee_id = ? WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setInt(1, updatedEmployee.getService_order_id());
			stmt.setInt(2, updatedEmployee.getEmployee_id());
			stmt.setString(3, uuid);
			stmt.executeUpdate();

			return Response.ok("Funcionário atualizado.").build();

		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(501).entity("Ops, erro ao atualizar funcionário.").build();
		}
	}
	
	@DELETE
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response deleteservice_order_employee(@PathParam("uuid") String uuid) {
		String query = "UPDATE service_order_employee SET deleted_at = CURRENT_TIMESTAMP WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Funcionário deletado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}

	@PUT
	@Path("/reactivate/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response reactivateUser(@PathParam("uuid") String uuid) {
		String query = "UPDATE service_order_employee SET deleted_at = null WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Funcionário reativado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}
}
