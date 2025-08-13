package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.EmployeeModel;
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
// "company_uuid": "TEST",
// "name": "Luis Zimermann",
// "role": "Motorista"
// }

@Path("/v1/employee")
public class EmployeeResource {

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getEmployees() {
		List<EmployeeModel> employee_list = new ArrayList<>();
		String query = "SELECT * FROM employee WHERE deleted_at IS NULL";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				EmployeeModel employee = new EmployeeModel();
				employee.setId			(rs.getInt		("id"));
				employee.setUuid		(rs.getString	("uuid"));
				employee.setCompany_id	(rs.getInt		("company_id"));
				employee.setName		(rs.getString	("name"));
				employee.setRole	    (rs.getString	("role"));
				employee.setCreated_at	(rs.getString	("created_at"));
				employee_list.add(employee);
			}

			return Response.ok(employee_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar funcionários.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createEmployee(EmployeeModel newEmployee) {
		String query = "INSERT INTO employee (uuid, company_id, name, role) VALUES (?, ?, ?, ?)";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString	(1,	UUID.randomUUID().toString());
			stmt.setInt		(2, 	newEmployee.getCompany_id());
			stmt.setString	(3, 	newEmployee.getName());
			stmt.setString	(4, 	newEmployee.getRole());
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
		String query = "SELECT * FROM employee WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					EmployeeModel employee = new EmployeeModel();
					employee.setId(rs.getInt("id"));
					employee.setUuid(rs.getString("uuid"));
					employee.setName(rs.getString("name"));
					employee.setRole(rs.getString("role"));
					employee.setCreated_at(rs.getString("created_at"));
					return Response.ok(employee).build();
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
		EmployeeModel updatedClient) {
		String query = "UPDATE employee SET name = ?, role = ? WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, updatedClient.getName());
			stmt.setString(2, updatedClient.getRole());
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
	public Response deleteemployee(@PathParam("uuid") String uuid) {
		String query = "UPDATE employee SET deleted_at = CURRENT_TIMESTAMP WHERE uuid = ?";

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
		String query = "UPDATE employee SET deleted_at = null WHERE uuid = ?";

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
