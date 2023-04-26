package com.lectura.backend.service;

import java.time.*;
import java.util.Base64;
import java.util.Objects;
import java.util.UUID;

public class TokenDescarga {
    private String token;
    private long milisecondExpiration;
    private int hashCode;

    public String getToken() {
        return token;
    }

    public long getMilisecondExpiration() {
        return milisecondExpiration;
    }

    public int getHashCode() {
        return hashCode;
    }

    public TokenDescarga() {
        this(LocalDateTime.now());
    }

    public TokenDescarga(LocalDateTime dateTimeExpiracion) {
        token = UUID.randomUUID().toString();
        var zonedDateTime = ZonedDateTime.of(dateTimeExpiracion, ZoneId.systemDefault());
        milisecondExpiration = zonedDateTime.toInstant().toEpochMilli();
        setHashCode();
    }

    public TokenDescarga(String uniqueToken) throws Exception {
        var stringToken = decodeBase64(uniqueToken);
        String[] tokens = stringToken.split("\\|");
        milisecondExpiration = Long.parseLong(tokens[0]);
        token = tokens[1];
        setHashCode();
        if (Integer.parseInt(tokens[2]) != hashCode) {
            throw new Exception("No coincide el Hash Code de los datos");
        }
    }

    public LocalDateTime getExpirationDateTime() throws Exception {
        if (milisecondExpiration <= 0) {
            throw new Exception("La clase no tiene el dato de Expiracion.");
        }
        return Instant.ofEpochMilli(milisecondExpiration).atZone(ZoneId.systemDefault()).toLocalDateTime();
    }

    @Override
    public String toString() {
        StringBuilder unique = new StringBuilder();
        unique.append(milisecondExpiration).append("|")
                .append(token).append("|")
                .append(hashCode);
        return encodeBase64(unique.toString());
    }

    private void setHashCode() {
        hashCode = Objects.hash(token, milisecondExpiration);
    }

    private String encodeBase64(String value) {
        return Base64.getUrlEncoder().withoutPadding().encodeToString(value.getBytes());
    }

    private String decodeBase64(String encodedURLString) {
        byte[] decodedURLBytes = Base64.getUrlDecoder().decode(encodedURLString);
        return new String(decodedURLBytes);
    }
}
