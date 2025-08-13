package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.CategoryServiceOrderModel;
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
//     "name": "Aluguel de guindaste",
// }

@Path("/v1/category/service")
public class CategoryServiceOrderResource {

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getServiceOrders() {
		List<CategoryServiceOrderModel> category_service_list = new ArrayList<>();
		String query = "SELECT * FROM category_service WHERE deleted_at IS NULL";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				CategoryServiceOrderModel service_order = new CategoryServiceOrderModel();
				service_order.setId			    (rs.getInt		("id"));
				service_order.setUuid		    (rs.getString	("uuid"));
				service_order.setName		    (rs.getString	("name"));
				service_order.setCreated_at	    (rs.getString	("created_at"));
				category_service_list.add(service_order);
			}

			return Response.ok(category_service_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar as categorias de serviço.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createCategoryServiceOrder(CategoryServiceOrderModel newCategoryServiceOrder) {
		String query = "INSERT INTO category_service (uuid, name) VALUES (?, ?)";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString	(1,	UUID.randomUUID().toString());
			stmt.setString	(2, 	newCategoryServiceOrder.getName());
			stmt.executeUpdate();

			ResponseMessage responseMessage = new ResponseMessage("Categoria cadastrada.");
			return Response.ok(responseMessage).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro ao cadastrar categoria de serviço.").build();
		}
	}

	@GET
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response getCategoryServiceOrderById(@PathParam("uuid") String uuid) {
		String query = "SELECT * FROM category_service WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					CategoryServiceOrderModel category_service = new CategoryServiceOrderModel();
                    category_service.setId			    (rs.getInt		("id"));
                    category_service.setUuid		    (rs.getString	("uuid"));
                    category_service.setName		    (rs.getString	("name"));
                    category_service.setCreated_at	    (rs.getString	("created_at"));
					return Response.ok(category_service).build();
				} else {
					return Response.status(404).entity("Categoria não encontrada.").build();
				}
			}
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Erro ao encontrar categoria.").build();
		}
	}

	@PUT
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public Response updateCategoryServiceOrder(
		@PathParam("uuid") String uuid,
		CategoryServiceOrderModel updatedCategoryService) {
		String query = "UPDATE category_service SET name = ?, WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString	(1, 	updatedCategoryService.getName());
			stmt.setString  (2, uuid);
			stmt.executeUpdate();

			return Response.ok("Categoria atualizada.").build();

		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(501).entity("Ops, erro ao atualizar categoria.").build();
		}
	}
	
	@DELETE
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response deleteCategoryService(@PathParam("uuid") String uuid) {
		String query = "UPDATE category_service SET deleted_at = CURRENT_TIMESTAMP WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Categoria deletado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}

	@PUT
	@Path("/reactivate/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response reactivateCategoryService(@PathParam("uuid") String uuid) {
		String query = "UPDATE category_service SET deleted_at = null WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Categoria reativada.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}
}
