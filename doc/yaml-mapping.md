Yaml mapping
============

If you don't (want to) use annotations for your doctrine mappings, you can use the yaml driver.

## Enabling the Yaml driver

To use the Yaml driver, you can replace the annotation driver with the Yaml driver.
 You should replace the following lines of the [getting started guide](getting-started.md):

```php
use Prezent\Doctrine\Translatable\Mapping\Driver\AnnotationDriver;

$annotationDriver = new AnnotationDriver($annotationReader);
$metadataFactory  = new MetadataFactory($annotationDriver);
```

with the code below:

```php
use Prezent\Doctrine\Translatable\Mapping\Driver\YamlDriver;

$yamlDriver = new YamlDriver($em);
$metadataFactory  = new MetadataFactory($yamlDriver);
```

## Yaml mapping example

```yaml
# BlogPost.orm.yml
BlogPost:
    prezent:
        translatable:
            field: translations # optional (default: translations)
            targetEntity: BlogPostTranslation # optional (default: [EntityName]Translation)
            currentLocale: currentLocale # optional
            fallbackLocale: fallbackLocale # optional
    fields:
        ...
```

```yaml
# BlogPostTranslation.orm.yml
BlogPostTranslation:
    prezent:
        translatable:
            field: translatable # optional (default: translatable)
            targetEntity: BlogPost # optional (default: entity name without "Translation" suffix)
    fields:
        locale:
            type: string
            length: 2
            prezent:
                translatable:
                    - locale
```
