package org.fortis.model;

import lombok.Data;

@Data
public class CategoryServiceOrderModel {
    private Integer id;
	private String uuid;
	private String name;
	private String created_at;
	private String deleted_at;
}
