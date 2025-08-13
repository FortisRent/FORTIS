package org.fortis.model;

import lombok.Data;

@Data
public class ServiceOrderEmployeeModel {
    private Integer id;
	private String uuid;
	private Integer service_order_id;
	private Integer employee_id;
	private String created_at;
	private String deleted_at;
}
