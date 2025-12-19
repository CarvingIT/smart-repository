---
layout: default
title: Smart Repository – Digital Asset Management System
---

# Smart Repository

**Smart Repository** is an open-source Digital Asset Management (DAM) system designed to serve as an institutional repository for digital content. Whether you're managing research papers, media files, or corporate documents, Smart Repository provides the tools you need.

![Smart Repository Hero]({{ site.baseurl }}/assets/images/features/hero.png)

---

## Why Smart Repository?

- **Lightning-fast search** – Elasticsearch-powered full-text search across thousands of documents.
- **Strong permission control** – Define who can view, edit, or manage each collection.
- **Revision history** – Track every change with automatic versioning.
- **OCR support** – Make scanned documents searchable.
- **Localization** – Interface and transliteration support for multiple languages.
- **Custom metadata** – Create your own cataloging fields and filters.
- **SSO & Approvals** – Integrate with SAML 2.0 providers and control publishing workflows.
- **API-first** – Seamless integration with other applications via REST API.
- **Cloud-ready** – Store files on S3, Azure, GCS, or local disk.

---

## Get Started

```bash
git clone https://github.com/CarvingIT/smart-repository.git
cd smart-repository
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000) to see it in action.

---

## Explore Features

Check out the [Features]({{ site.baseurl }}/features/) section for detailed descriptions and screenshots.

---

## Support & Contact

- **Email:** [info@carvingit.com](mailto:info@carvingit.com)
- **Phone:** +91 94201 21704
- **GitHub Issues:** [Open an issue](https://github.com/CarvingIT/smart-repository/issues)

---

<small>Smart Repository is maintained by [Carving IT](https://www.carvingit.com).</small>
