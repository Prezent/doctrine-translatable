Xml mapping
============

If you don't (want to) use annotations for your doctrine mappings, you can use the xml driver.

## Enabling the Xml driver

You can enable the Xml drive manually using the following code. This allows you to keep the Doctrine mappings separate
from your translatable mappings.

```php
use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use Prezent\Doctrine\Translatable\Mapping\Driver\XmlDriver;

$locator = new DefaultFileLocator(...);
$xmlDriver = new XmlDriver($locator);
$metadataFactory  = new MetadataFactory($xmlDriver);
```

## Xml mapping example

```xml
<!-- BlogPost.orm.xml -->
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xmlns:prezent="https://prezent.nl/schemas/doctrine-translatable"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd
                                      https://prezent.nl/schemas/doctrine-translatable
                                      https://prezent.nl/schemas/doctrine-translatable-3.0.xsd">

    ...

        <prezent:translatable 
            translations="translations" 
            target-entity="BlogPostTranslation" 
            current-locale="currentLocale" 
            fallback-locale="fallbackLocale"/>

    ...

</doctrine-mapping>
```

```xml
<!-- BlogPostTranslation.orm.xml -->
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xmlns:prezent="https://prezent.nl/schemas/doctrine-translatable"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd
                                      https://prezent.nl/schemas/doctrine-translatable
                                      https://prezent.nl/schemas/doctrine-translatable-3.0.xsd">

    ...

        <prezent:translation translatable="translatable" target-entity="BlogPost" referenced-column-name="id" locale="locale"/>

    ...

</doctrine-mapping>
```
