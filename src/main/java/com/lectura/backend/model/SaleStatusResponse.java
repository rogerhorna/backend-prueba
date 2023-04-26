package com.lectura.backend.model;

import lombok.Data;
import java.time.LocalDateTime;

@Data
public class SaleStatusResponse {
    private String orderId;
    private String customer;
    private LocalDateTime dateTime;
    private String sku;
    private String format;
    private boolean downloaded;
}
