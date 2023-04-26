package com.lectura.backend.service;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;

import java.time.LocalDateTime;

public class TokenDescargaTest {
    @Test
    public void generateToken() throws Exception {
        var tokenDescarga = new TokenDescarga();
        var token = tokenDescarga.toString();

        System.out.println("token: " + token);
        Assertions.assertTrue(!token.isEmpty());

        System.out.println("Milisecond Expiracion: " + tokenDescarga.getMilisecondExpiration());
        System.out.println("Token: " + tokenDescarga.getToken());
        System.out.println("Hash Code: " + tokenDescarga.getHashCode());
        System.out.println("datetime: " + tokenDescarga.getExpirationDateTime());
        System.out.println("==============================");
    }

    @Test
    public void generateTokenFromString() throws Exception {
        var tokenDescarga = new TokenDescarga("MTY2NDk0ODgxOTc0M3xkYTViZTQwNy03NzZhLTRkZjktOWNiZC0wOGMyM2U1ZjdjNjh8LTUzMjkzMzk3NQ");
        var token = tokenDescarga.toString();

        System.out.println("token: " + token);
        Assertions.assertTrue(!token.isEmpty());

        System.out.println("Milisecond Expiracion: " + tokenDescarga.getMilisecondExpiration());
        System.out.println("Token: " + tokenDescarga.getToken());
        System.out.println("Hash Code: " + tokenDescarga.getHashCode());
        System.out.println("datetime: " + tokenDescarga.getExpirationDateTime());
        System.out.println("==============================");
    }

    @Test
    public void generateTokenWithDatetime() throws Exception {
        var tokenDescarga = new TokenDescarga(LocalDateTime.now().plusDays(1));
        var token = tokenDescarga.toString();

        System.out.println("token: " + token);
        Assertions.assertTrue(!token.isEmpty());

        System.out.println("Milisecond Expiracion: " + tokenDescarga.getMilisecondExpiration());
        System.out.println("Token: " + tokenDescarga.getToken());
        System.out.println("Hash Code: " + tokenDescarga.getHashCode());
        System.out.println("datetime: " + tokenDescarga.getExpirationDateTime());
        System.out.println("==============================");
    }
}
