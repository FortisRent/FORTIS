package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.GearModel;
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
// "name": "Luis Zimermann"
// }

@Path("/v1/gear")
public class GearResource {

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getEmployees() {
		List<GearModel> gear_list = new ArrayList<>();
		String query = "SELECT * FROM gear WHERE deleted_at IS NULL";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				GearModel gear = new GearModel();
				gear.setId			(rs.getInt		("id"));
				gear.setUuid		(rs.getString	("uuid"));
				gear.setCompany_id	(rs.getInt		("company_id"));
				gear.setName		(rs.getString	("name"));
				gear.setCreated_at	(rs.getString	("created_at"));
				gear_list.add(gear);
			}

			return Response.ok(gear_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar equipamentos.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createEmployee(GearModel newEmployee) {
		String query = "INSERT INTO gear (uuid, company_id, name) VALUES (?, ?, ?)";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString	(1,	UUID.randomUUID().toString());
			stmt.setInt		(2, 	newEmployee.getCompany_id());
			stmt.setString	(3, 	newEmployee.getName());
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
		String query = "SELECT * FROM gear WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					GearModel gear = new GearModel();
					gear.setId(rs.getInt("id"));
					gear.setUuid(rs.getString("uuid"));
					gear.setCompany_id(rs.getInt("id"));
					gear.setName(rs.getString("name"));
					gear.setCreated_at(rs.getString("created_at"));
					return Response.ok(gear).build();
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
		GearModel updatedClient) {
		String query = "UPDATE gear SET name = ? WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, updatedClient.getName());
			stmt.setString(2, uuid);
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
	public Response deletegear(@PathParam("uuid") String uuid) {
		String query = "UPDATE gear SET deleted_at = CURRENT_TIMESTAMP WHERE uuid = ?";

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
		String query = "UPDATE gear SET deleted_at = null WHERE uuid = ?";

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
