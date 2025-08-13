package org.fortis.model;

import lombok.Data;

@Data
public class EmployeeModel {
    private Integer id;
	private String uuid;
	private Integer company_id;
	private String name;
	private String role;
	private String created_at;
	private String deleted_at;
}