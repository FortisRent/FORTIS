package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.time.ZoneId;
import java.time.ZonedDateTime;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.UserModel;
import org.fortis.service.Db;
import org.fortis.service.ResponseMessage;

import com.google.common.base.Charsets;
import com.google.common.hash.Hashing;

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

// 	{
//  	"name": "Luis",
//  	"email": "luis@gmail.com",
//  	"cpf": "000.000.000-00"
//  	"password": "12345"
// 	}

@Path("/v1/user")
public class UserResource {

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getUsers() {
		List<UserModel> user_list = new ArrayList<>();
		String query = "SELECT * FROM user WHERE deleted_at IS NULL";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				UserModel user = new UserModel();
				user.setId			(rs.getInt		("id"));
				user.setUuid		(rs.getString	("uuid"));
				user.setName		(rs.getString	("name"));
				user.setPhone		(rs.getString	("phone"));
				user.setCpf			(rs.getString	("cpf"));
				user.setEmail		(rs.getString	("email"));
				user.setPassword	(rs.getString	("password"));
				user.setCreated_at	(rs.getString	("created_at"));
				user_list.add(user);
			}

			return Response.ok(user_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar usuários.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createUser(UserModel newUser) {
		String query = "INSERT INTO user (uuid, name, phone, cpf, email, password) VALUES (?, ?, ?, ?, ?, ?)";

        try (Connection conn = Db.connect();
            PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString	(1,	UUID.randomUUID().toString());
            stmt.setString	(2, 	newUser.getName());
            stmt.setString	(3, 	newUser.getPhone());
            stmt.setString	(4, 	newUser.getCpf());
            stmt.setString	(5, 	newUser.getEmail());
            stmt.setString	(6, 	Hashing.sha256().hashString(newUser.getPassword(), Charsets.UTF_8).toString());

            stmt.executeUpdate();

			ResponseMessage responseMessage = new ResponseMessage("Usuário cadastrado");
			return Response.ok(responseMessage).build();
        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(500).entity("Ops, erro ao cadastrar usuário.").build();
        }
	}

	@GET
	@Path("/{id}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response getUserById(@PathParam("id") Long id) {
		String query = "SELECT * FROM user WHERE id = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setLong(1, id);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					UserModel user = new UserModel();
					user.setId(rs.getInt("id"));
					user.setUuid(rs.getString("uuid"));
					user.setName(rs.getString("name"));
					user.setPhone(rs.getString("phone"));
					user.setCpf(rs.getString("cpf"));
					user.setEmail(rs.getString("email"));
					user.setPassword(rs.getString("password"));
					return Response.ok(user).build();
				} else {
					return Response.status(404).entity("Usuário não encontrada.").build();
				}
			}
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Erro ao cadastrar usuário").build();
		}
	}

	@PUT
	@Path("/{id}/")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public Response updateUser(
		@PathParam("id") Integer id,
		UserModel updatedUser) {
		String query = "UPDATE user SET name = ?, phone = ?, cpf = ?, email = ? WHERE id = ?";

        try (Connection conn = Db.connect();
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString(1, updatedUser.getName());
            stmt.setString(2, updatedUser.getPhone());
            stmt.setString(3, updatedUser.getCpf());
            stmt.setString(4, updatedUser.getEmail());
            stmt.setInt(5, id);
            stmt.executeUpdate();

			return Response.ok("Usuário atualizado.").build();

        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(501).entity("Ops, erro ao atualizar usuário").build();
        }
	}
	
	@DELETE
	@Path("/{id}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response deleteuser(@PathParam("id") Integer id) {
		String query = "UPDATE user SET deleted_at = ? WHERE id = ?";

		ZonedDateTime zonedDateTime = ZonedDateTime.now(ZoneId.of("America/Sao_Paulo"));

		Timestamp timestamp = Timestamp.valueOf(zonedDateTime.toLocalDateTime());

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setTimestamp(1, timestamp);
			stmt.setInt(2, id);

			stmt.executeUpdate();
			return Response.ok("Usuário deletado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado").build();
		}
	}

	@PUT
	@Path("/reactivate/{id}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response reactivateUser(@PathParam("id") Integer id) {
		String query = "UPDATE user SET deleted_at = null WHERE id = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setInt(1, id);

			stmt.executeUpdate();
			return Response.ok("Usuário reativado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado").build();
		}
	}
}
