package org.fortis.model;

import lombok.Data;

@Data
public class ServiceOrderGearModel {
    private Integer id;
	private String uuid;
	private Integer service_order_id;
	private Integer gear_id;
	private String created_at;
	private String deleted_at;
}
