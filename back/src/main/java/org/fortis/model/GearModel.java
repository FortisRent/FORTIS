package org.fortis.model;

import lombok.Data;

@Data
public class GearModel {
    private Integer id;
	private String uuid;
	private Integer company_id;
	private String name;
	private String created_at;
	private String deleted_at;
}