package com.lectura.backend.util;

import java.time.LocalDateTime;
import java.time.ZoneId;
import java.time.ZonedDateTime;
import java.time.format.DateTimeFormatter;

public class DateUtils {
    private final static DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyyMMdd");

    public static Integer getMadridDate() {
        ZonedDateTime zonedDateTime = LocalDateTime.now().atZone(ZoneId.systemDefault());

        ZoneId madrid = ZoneId.of("Europe/Madrid");
        ZonedDateTime madridDatetime  = zonedDateTime.withZoneSameInstant(madrid);
        var result = madridDatetime.format(formatter);
        return Integer.parseInt(result);
    }
}
