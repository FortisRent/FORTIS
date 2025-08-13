package org.fortis.model;

import lombok.Data;

@Data
public class ServiceOrderModel {
    private Integer id;
	private String uuid;
	private String code;
	private String name;
	private Integer client_id;
	private Integer status_id;
	private Integer company_id;
	private String start_date;
	private String start_time;
	private String end_time;
	private String address;
	private String service_load;
	private String service_width;
	private String service_height;
	private String service_length;
	private String created_at;
	private String deleted_at;
}
