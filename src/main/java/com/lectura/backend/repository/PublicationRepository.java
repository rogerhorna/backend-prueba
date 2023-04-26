package com.lectura.backend.repository;

import com.lectura.backend.entity.Publication;
import io.quarkus.hibernate.orm.panache.PanacheRepositoryBase;
import io.quarkus.panache.common.Page;

import javax.enterprise.context.ApplicationScoped;
import java.util.List;

@ApplicationScoped
public class PublicationRepository implements PanacheRepositoryBase<Publication, String> {
    public List<Publication> findToSynchronize(Integer date) {
        return list("(updated = ?1 OR productId = null) AND publishingDate <= ?2 AND marketDate <= ?2", false, date);
    }

    public List<Publication> findToSynchronize(int page, int size, Integer date) {
        return find("(updated = ?1 OR productId = null) AND publishingDate <= ?2 AND marketDate <= ?2", false, date)
                .page(Page.of(page, size)).list();
    }

    public Publication findByIsbn(String isbn) {
        return find("isbn", isbn).singleResult();
    }

    public Publication findByProductId(Long productId) {
        return find("productId", productId).singleResult();
    }
}
