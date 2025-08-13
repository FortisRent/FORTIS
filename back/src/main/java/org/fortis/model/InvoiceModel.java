package org.fortis.model;

import lombok.Data;

@Data
public class InvoiceModel {
    private Integer id;
    private String uuid;
    private String fileUrl;
    private Integer amount;
    private String description;
}
