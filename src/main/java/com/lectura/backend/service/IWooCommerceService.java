package com.lectura.backend.service;

import com.lectura.backend.model.OrderDto;
import com.lectura.backend.model.SaleStatusResponse;
import com.lectura.backend.model.SimulateSaleResponse;

import javax.transaction.*;
import java.net.URI;

public interface IWooCommerceService {
    boolean synchronization();

    SimulateSaleResponse simulateSale(Long productId, Double price) throws Exception;

    String registerSale(OrderDto order) throws Exception;

    String regenerateSaleToken(String orderId) throws Exception;

    URI getDownloadUrl(String orderId, String uname) throws Exception;

    SaleStatusResponse getSaleStatus(String orderId);
}
