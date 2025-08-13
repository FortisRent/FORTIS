package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.ClientModel;
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
 	// "name": "Luis",
 	// "email": "luis@gmail.com",
 	// "phone": "(48) 90000-1234"
 	// "cpf": "000.000.000-00"
 	// "password": "12345"
	// }

@Path("/v1/client")
public class ClientResource {

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getClients() {
		List<ClientModel> client_list = new ArrayList<>();
		String query = "SELECT * FROM client WHERE deleted_at IS NULL";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				ClientModel client = new ClientModel();
				client.setId			(rs.getInt		("id"));
				client.setUuid		    (rs.getString	("uuid"));
				client.setName		    (rs.getString	("name"));
				client.setPhone		    (rs.getString	("phone"));
				client.setEmail		    (rs.getString	("email"));
				client.setCreated_at	(rs.getString	("created_at"));
				client_list.add(client);
			}

			return Response.ok(client_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar clientes.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createClient(ClientModel newClient) {
		String query = "INSERT INTO client (uuid, name, phone, email) VALUES (?, ?, ?, ?)";

        try (Connection conn = Db.connect();
            PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString	(1,	UUID.randomUUID().toString());
            stmt.setString	(2, 	newClient.getName());
            stmt.setString	(3, 	newClient.getPhone());
            stmt.setString	(4, 	newClient.getEmail());

            stmt.executeUpdate();

			ResponseMessage responseMessage = new ResponseMessage("Cliente cadastrado");
			return Response.ok(responseMessage).build();
        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(500).entity("Ops, erro ao cadastrar cliente..").build();
        }
	}

	@GET
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response getClientById(@PathParam("uuid") String uuid) {
		String query = "SELECT * FROM client WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					ClientModel client = new ClientModel();
					client.setId(rs.getInt("id"));
					client.setUuid(rs.getString("uuid"));
					client.setName(rs.getString("name"));
					client.setPhone(rs.getString("phone"));
					client.setEmail(rs.getString("email"));
					client.setCreated_at(rs.getString("created_at"));
					return Response.ok(client).build();
				} else {
					return Response.status(404).entity("Cliente não encontrado.").build();
				}
			}
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Erro ao cadastrar cliente.").build();
		}
	}

	@PUT
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public Response updateClient(
		@PathParam("uuid") String uuid,
		ClientModel updatedClient) {
		String query = "UPDATE client SET name = ?, phone = ?, email = ? WHERE uuid = ?";

        try (Connection conn = Db.connect();
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString(1, updatedClient.getName());
            stmt.setString(2, updatedClient.getPhone());
            stmt.setString(3, updatedClient.getEmail());
            stmt.setString(4, uuid);
            stmt.executeUpdate();

			return Response.ok("Cliente atualizado.").build();

        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(501).entity("Ops, erro ao atualizar cliente.").build();
        }
	}
	
	@DELETE
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response deleteclient(@PathParam("uuid") String uuid) {
		String query = "UPDATE client SET deleted_at = CURRENT_TIMESTAMP WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Cliente deletado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado").build();
		}
	}

	@PUT
	@Path("/reactivate/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response reactivateClient(@PathParam("uuid") String uuid) {
		String query = "UPDATE client SET deleted_at = null WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Cliente reativado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado").build();
		}
	}
}
