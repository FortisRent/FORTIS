package org.fortis.resource;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

import org.fortis.model.CompanyModel;
import org.fortis.service.Db;
import org.fortis.service.ResponseMessage;

import jakarta.ws.rs.Produces;
import jakarta.ws.rs.Consumes;
import jakarta.ws.rs.DELETE;
import jakarta.ws.rs.GET;
import jakarta.ws.rs.POST;
import jakarta.ws.rs.PUT;
import jakarta.ws.rs.Path;
import jakarta.ws.rs.PathParam;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;

// 	{
//  	"name": "RodoBras",
//  	"cnpj": "00000000000"
// 	}

@Path("/v1/company")
public class CompanyResource {

    @GET
    @Produces(MediaType.APPLICATION_JSON)
    @Path("/")
    public Response getCompanies(){
        List<CompanyModel> company_list = new ArrayList<>();
        String query = "SELECT * FROM company WHERE deleted_at IS NULL";

        try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query);
			ResultSet rs = stmt.executeQuery()) {

			while (rs.next()) {
				CompanyModel company =  new CompanyModel();
				company.setId			(rs.getInt		("id"));
				company.setUuid		    (rs.getString	("uuid"));
				company.setName		    (rs.getString	("name"));
				company.setCnpj		    (rs.getString	("cnpj"));
				company.setCreated_at	(rs.getString	("created_at"));
				company_list.add(company);
			}

			return Response.ok(company_list).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Ops, erro para listar empresas.").build();
		}
    }

    @POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	@Path("/")
	public Response createCompany(CompanyModel newCompany) {
		String query = "INSERT INTO company (uuid, name, cnpj) VALUES (?, ?, ?)";

        try (Connection conn = Db.connect();
            PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString	(1,	UUID.randomUUID().toString());
            stmt.setString	(2, 	newCompany.getName());
            stmt.setString	(3, 	newCompany.getCnpj());

            stmt.executeUpdate();

			ResponseMessage responseMessage = new ResponseMessage("Empresa cadastrada.");
			return Response.ok(responseMessage).build();
        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(500).entity("Ops, erro ao cadastrar empresa.").build();
        }
	}

    @GET
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response getCompanyByUuid(@PathParam("uuid") String uuid) {
		String query = "SELECT * FROM company WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			try (ResultSet rs = stmt.executeQuery()) {
				if (rs.next()) {
					CompanyModel company = new CompanyModel();
					company.setId           (rs.getInt      ("id"));
					company.setUuid         (rs.getString   ("uuid"));
					company.setName         (rs.getString   ("name"));
					company.setCnpj         (rs.getString   ("cnpj"));
					company.setCreated_at   (rs.getString   ("created_at"));
					return Response.ok(company).build();
				} else {
					return Response.status(404).entity("Empresa não encontrada.").build();
				}
			}
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(500).entity("Erro ao cadastrar empresa.").build();
		}
	}

    @PUT
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public Response updateCompany(
		@PathParam("uuid") String uuid,
		CompanyModel updatedCompany) {
		String query = "UPDATE company SET name = ?, cnpj = ? WHERE uuid = ?";

        try (Connection conn = Db.connect();
            PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString(1, updatedCompany.getName());
            stmt.setString(2, updatedCompany.getCnpj());
            stmt.setString(3, uuid);
            stmt.executeUpdate();

			ResponseMessage responseMessage = new ResponseMessage("Empresa atualizada.");
			return Response.ok(responseMessage).build();
        } catch (SQLException e) {
            e.printStackTrace();
			return Response.status(501).entity("Ops, erro ao atualizar empresa.").build();
        }
	}

    @DELETE
	@Path("/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response deleteCompany(@PathParam("uuid") String uuid) {
		String query = "UPDATE company SET deleted_at = CURRENT_TIMESTAMP WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			ResponseMessage responseMessage = new ResponseMessage("Empresa deletada.");
			return Response.ok(responseMessage).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}

	@PUT
	@Path("/reactivate/{uuid}/")
	@Produces(MediaType.APPLICATION_JSON)
	public Response reactivateCompany(@PathParam("uuid") String uuid) {
		String query = "UPDATE company SET deleted_at = null WHERE uuid = ?";

		try (Connection conn = Db.connect();
			PreparedStatement stmt = conn.prepareStatement(query)) {

			stmt.setString(1, uuid);

			stmt.executeUpdate();
			ResponseMessage responseMessage = new ResponseMessage("Empresa reativada.");
			return Response.ok(responseMessage).build();
		} catch (SQLException e) {
			e.printStackTrace();
			return Response.status(404, "Id não encontrado.").build();
		}
	}
}
