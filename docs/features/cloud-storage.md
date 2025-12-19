---
layout: default
title: Cloud Storage
permalink: /features/cloud-storage/
---

# Cloud Storage

Store documents on configurable cloud backends.

## Supported providers

- Amazon S3
- Azure Blob Storage
- Google Cloud Storage
- Local disk

![Cloud Storage Screenshot]({{ site.baseurl }}/assets/images/features/cloud-storage.png)

## Configuration

Set the storage driver in `.env`:

```
FILESYSTEM_DISK=s3
```

[← Back to Features]({{ site.baseurl }}/features/)
