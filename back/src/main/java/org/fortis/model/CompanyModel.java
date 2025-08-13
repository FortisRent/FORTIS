package org.fortis.model;

import lombok.Data;

@Data
public class CompanyModel {
    private Integer id;
    private String uuid; 
    private String name; 
    private String cnpj; 
    private String created_at; 
}
