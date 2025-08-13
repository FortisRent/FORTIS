package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.ServiceOrderGearModel;
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
	//     "gear_id": 1
	// }

@Path("/v1/so/gear")
public class ServiceOrderGearResource {

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getEmployees() {
		List<ServiceOrderGearModel> service_order_gear_list = new ArrayList<>();
		String query = "SELECT * FROM service_order_gear WHERE deleted_at IS NULL";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				ServiceOrderGearModel service_order_gear = new ServiceOrderGearModel();
				service_order_gear.setId			        (rs.getInt		("id"));
				service_order_gear.setUuid		            (rs.getString	("uuid"));
				service_order_gear.setService_order_id      (rs.getInt		("service_order_id"));
				service_order_gear.setGear_id           	(rs.getInt	    ("gear_id"));
				service_order_gear.setCreated_at	        (rs.getString	("created_at"));
				service_order_gear_list.add(service_order_gear);
			}

			return Response.ok(service_order_gear_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar equipamentos.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createEmployee(ServiceOrderGearModel newGear) {
		String query = "INSERT INTO service_order_gear (uuid, service_order_id, gear_id) VALUES (?, ?, ?)";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString	(1,	UUID.randomUUID().toString());
			stmt.setInt		(2, 	newGear.getService_order_id());
			stmt.setInt		(3, 	newGear.getGear_id());
			stmt.executeUpdate();

			ResponseMessage responseMessage = new ResponseMessage("Equipamento cadastrado.");
			return Response.ok(responseMessage).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro ao cadastrar equipamento.").build();
		}
	}

	@GET
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response getEmployeeById(@PathParam("uuid") String uuid) {
		String query = "SELECT * FROM service_order_gear WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					ServiceOrderGearModel service_order_gear = new ServiceOrderGearModel();
					service_order_gear.setId(rs.getInt("id"));
					service_order_gear.setUuid(rs.getString("uuid"));
					service_order_gear.setService_order_id(rs.getInt("service_order_id"));
					service_order_gear.setGear_id(rs.getInt("gear_id"));
					service_order_gear.setCreated_at(rs.getString("created_at"));
					return Response.ok(service_order_gear).build();
				} else {
					return Response.status(404).entity("Equipamento não encontrado.").build();
				}
			}
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Erro ao encontrar equipamento.").build();
		}
	}

	@PUT
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public Response updateEmployee(
		@PathParam("uuid") String uuid,
		ServiceOrderGearModel updatedGear) {
		String query = "UPDATE service_order_gear SET service_order_id = ?, emplyee_id = ? WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setInt(1, updatedGear.getService_order_id());
			stmt.setInt(2, updatedGear.getGear_id());
			stmt.setString(3, uuid);
			stmt.executeUpdate();

			return Response.ok("Equipamento atualizado.").build();

		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(501).entity("Ops, erro ao atualizar equipamento.").build();
		}
	}
	
	@DELETE
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response deleteservice_order_gear(@PathParam("uuid") String uuid) {
		String query = "UPDATE service_order_gear SET deleted_at = CURRENT_TIMESTAMP WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Equipamento deletado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}

	@PUT
	@Path("/reactivate/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response reactivateUser(@PathParam("uuid") String uuid) {
		String query = "UPDATE service_order_gear SET deleted_at = null WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Equipamento reativado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}
}
