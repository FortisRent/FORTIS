package org.fortis.resource;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.InvoiceModel;

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
//  	"amount": 1500000,
//  	"description": "Universal",
//  	"file_url": "lalala"
// 	}

@Path("/v1/invoice")
public class InvoiceResource {
	public static List<InvoiceModel> invoice_list = new ArrayList<>();

	private static final String DB_URL = "jdbc:mysql://195.179.239.102:3306/u234488260_2025_erp";
	private static final String DB_USER = "u234488260_2025_erp";
	private static final String DB_PASSWORD = "t1O9&VeM|iF~";

	@GET
	@Produces(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response getInvoices() {
		List<InvoiceModel> invoices = new ArrayList<>();
		String query = "SELECT * FROM invoice";

		try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
			 PreparedStatement stmt = conn.prepareStatement(query);
			 ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				InvoiceModel invoice = new InvoiceModel();
				invoice.setId			(rs.getInt		("id"));
				invoice.setUuid			(rs.getString	("uuid"));
				invoice.setFileUrl		(rs.getString	("file_url"));
				invoice.setAmount		(rs.getInt		("amount"));
				invoice.setDescription	(rs.getString	("description"));
				invoices.add(invoice);
			}

			return Response.ok(invoices).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar notas fiscais.").build();
		}
	}

	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createInvoice(InvoiceModel newInvoice) {
		String query = "INSERT INTO invoice (uuid, file_url, amount, description) VALUES (?, ?, ?, ?)";

        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString	(1,	UUID.randomUUID().toString());
            stmt.setString	(2, 	newInvoice.getFileUrl());
            stmt.setInt		(3, 	newInvoice.getAmount());
            stmt.setString	(4, 	newInvoice.getDescription());

            stmt.executeUpdate();
			return Response.ok("Nota fiscal cadastrada").build();
        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(500).entity("Ops, erro ao cadastrar nota fiscal.").build();
        }
	}

	@GET
	@Path("/{id}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response getInvoiceById(@PathParam("id") Long id) {
		String query = "SELECT * FROM invoice WHERE id = ?";

		try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
			 PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setLong(1, id);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					InvoiceModel invoice = new InvoiceModel();
					invoice.setId(rs.getInt("id"));
					invoice.setUuid(rs.getString("uuid"));
					invoice.setFileUrl(rs.getString("file_url"));
					invoice.setAmount(rs.getInt("amount"));
					invoice.setDescription(rs.getString("description"));
					return Response.ok(invoice).build();
				} else {
					return Response.status(404).entity("Nota fiscal não encontrada.").build();
				}
			}
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Erro ao cadastrar nota fiscal").build();
		}
	}

	@PUT
	@Path("/{id}/")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public Response updateInvoice(
		@PathParam("id") Integer id,
		InvoiceModel updatedInvoice) {
		String query = "UPDATE invoice SET file_url = ?, amount = ?, description = ? WHERE id = ?";

        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString(1, updatedInvoice.getFileUrl());
            stmt.setInt(2, updatedInvoice.getAmount());
            stmt.setString(3, updatedInvoice.getDescription());
            stmt.setInt(4, id);
            stmt.executeUpdate();

			return Response.ok("Nota fiscal atualizada.").build();

        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(501).entity("Ops, erro ao atualizar nota fiscal").build();
        }
	}
	
	@DELETE
	@Path("/{id}")
	@Produces(MediaType.APPLICATION_JSON)
	public Response deleteinvoice(@PathParam("id") Integer id) {
		String query = "DELETE FROM invoice WHERE id = ?";

        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setInt(1, id);

            stmt.executeUpdate();
			return Response.ok("Nota fiscal deletada.").build();
        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(404, "Id não encontrado").build();
        }
	}
}
