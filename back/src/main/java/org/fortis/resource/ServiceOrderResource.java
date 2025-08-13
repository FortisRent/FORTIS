package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.ServiceOrderModel;
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
//     "client_id": 1,
//     "status_id": 1,
//     "company_id": 1,
//     "start_date": "20/12/2020",
//     "start_time": "7:30",
//     "end_time": "18:00",
//     "address": "Teste Endereço, 1234",
//     "service_load":15,
//     "service_width":15,
//     "service_height":15,
//     "service_length":15,
// }

@Path("/v1/service/order")
public class ServiceOrderResource {

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getServiceOrders() {
		List<ServiceOrderModel> service_order_list = new ArrayList<>();
		String query = "SELECT * FROM service_order WHERE deleted_at IS NULL";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				ServiceOrderModel service_order = new ServiceOrderModel();
				service_order.setId			    (rs.getInt		("id"));
				service_order.setUuid		    (rs.getString	("uuid"));
				service_order.setCode		    (rs.getString	("code"));
				service_order.setName		    (rs.getString	("name"));
				service_order.setClient_id      (rs.getInt      ("client_id"));
				service_order.setStatus_id      (rs.getInt      ("status_id"));
				service_order.setCompany_id	    (rs.getInt		("company_id"));
				service_order.setStart_date     (rs.getString   ("start_date"));
				service_order.setStart_time     (rs.getString   ("start_time"));
				service_order.setEnd_time       (rs.getString   ("end_time"));
				service_order.setAddress        (rs.getString   ( "address"));
				service_order.setService_load   (rs.getString   ( "service_load"));
				service_order.setService_width  (rs.getString   ( "service_width"));
				service_order.setService_height (rs.getString   ( "service_height"));
				service_order.setService_length (rs.getString   ( "service_length"));
				service_order.setCreated_at	    (rs.getString	("created_at"));
				service_order_list.add(service_order);
			}

			return Response.ok(service_order_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar funcionários.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createServiceOrder(ServiceOrderModel newServiceOrder) {
		String query = "INSERT INTO service_order (uuid, name, code, client_id, status_id, company_id, start_date, start_time, end_time, address, service_load, service_width, service_height, service_length) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString	(1,	UUID.randomUUID().toString());
			stmt.setString	(2, 	newServiceOrder.getName());
			stmt.setString  (3, 	newServiceOrder.getCode());
			stmt.setInt	    (4, 	newServiceOrder.getClient_id());
			stmt.setInt	    (5, 	newServiceOrder.getStatus_id());
			stmt.setInt	    (6, 	newServiceOrder.getCompany_id());
			stmt.setString	(7, 	newServiceOrder.getStart_date());
			stmt.setString	(8, 	newServiceOrder.getStart_time());
			stmt.setString	(9, 	newServiceOrder.getEnd_time());
			stmt.setString	(10, newServiceOrder.getAddress());
			stmt.setString	(11, newServiceOrder.getService_load());
			stmt.setString	(12, newServiceOrder.getService_width());
			stmt.setString	(13, newServiceOrder.getService_height());
			stmt.setString	(14, newServiceOrder.getService_length());
			stmt.executeUpdate();

			ResponseMessage responseMessage = new ResponseMessage("Serviço cadastrado.");
			return Response.ok(responseMessage).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro ao cadastrar funcionário.").build();
		}
	}

	@GET
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response getServiceOrderById(@PathParam("uuid") String uuid) {
		String query = "SELECT * FROM service_order WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					ServiceOrderModel service_order = new ServiceOrderModel();
                    service_order.setId			    (rs.getInt		("id"));
                    service_order.setUuid		    (rs.getString	("uuid"));
                    service_order.setCode		    (rs.getString	("code"));
                    service_order.setName		    (rs.getString	("name"));
                    service_order.setClient_id      (rs.getInt      ("client_id"));
                    service_order.setStatus_id      (rs.getInt      ("status_id"));
                    service_order.setCompany_id	    (rs.getInt		("company_id"));
                    service_order.setStart_date     (rs.getString   ("start_date"));
                    service_order.setStart_time     (rs.getString   ("start_time"));
                    service_order.setEnd_time       (rs.getString   ("end_time"));
                    service_order.setAddress        (rs.getString   ( "address"));
                    service_order.setService_load   (rs.getString   ( "service_load"));
                    service_order.setService_width  (rs.getString   ( "service_width"));
                    service_order.setService_height (rs.getString   ( "service_height"));
                    service_order.setService_length (rs.getString   ( "service_length"));
                    service_order.setCreated_at	    (rs.getString	("created_at"));
					return Response.ok(service_order).build();
				} else {
					return Response.status(404).entity("Serviço não encontrado.").build();
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
	public Response updateServiceOrder(
		@PathParam("uuid") String uuid,
		ServiceOrderModel updatedClient) {
		String query = "UPDATE service_order SET name = ?, client_id = ?, status_id = ?, company_id = ?, start_date = ?, start_time = ?, end_time = ?, address = ?, service_load = ?, service_width = ?, service_height = ?, service_length = ? WHERE uuid = ?";

		try (Connection conn = Db.connect();
			 PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString	(1, 	updatedClient.getName());
            stmt.setInt	    (2, 	updatedClient.getClient_id());
            stmt.setInt	    (3, 	updatedClient.getStatus_id());
            stmt.setInt	    (4, 	updatedClient.getCompany_id());
            stmt.setString	(5, 	updatedClient.getStart_date());
            stmt.setString	(6, 	updatedClient.getStart_time());
            stmt.setString	(7, 	updatedClient.getEnd_time());
            stmt.setString	(8, 	updatedClient.getAddress());
            stmt.setString	(9,  updatedClient.getService_load());
            stmt.setString	(10, updatedClient.getService_width());
            stmt.setString	(11, updatedClient.getService_height());
            stmt.setString	(12, updatedClient.getService_length());
			stmt.setString  (13, uuid);
			stmt.executeUpdate();

			return Response.ok("Serviço atualizado.").build();

		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(501).entity("Ops, erro ao atualizar funcionário.").build();
		}
	}
	
	@DELETE
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response deleteservice_order(@PathParam("uuid") String uuid) {
		String query = "UPDATE service_order SET deleted_at = CURRENT_TIMESTAMP WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Serviço deletado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}

	@PUT
	@Path("/reactivate/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response reactivateUser(@PathParam("uuid") String uuid) {
		String query = "UPDATE service_order SET deleted_at = null WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			return Response.ok("Serviço reativado.").build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}
}
