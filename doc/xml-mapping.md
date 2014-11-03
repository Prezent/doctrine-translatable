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
# BlogPost.orm.xml
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xmlns:gedmo="http://gediminasm.org/schemas/orm/doctrine-extensions-mapping"
                  xmlns:prezent="prezent"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    ...

        <!-- field="translations" #optional (default: translations) -->
        <!-- target-entity="BlogPostTranslation" #optional (default: [EntityName]Translation) -->
        <!-- current-locale="currentLocale" fallback-locale="fallbackLocale"  #optional -->
        <!-- fallback-locale="fallbackLocale" #optional -->
        <prezent:translatable field="translations" target-entity="BlogPostTranslation" current-locale="currentLocale" fallback-locale="fallbackLocale"/>

    ...

</doctrine-mapping>
```

```xml
# BlogPostTranslation.orm.xml
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xmlns:gedmo="http://gediminasm.org/schemas/orm/doctrine-extensions-mapping"
                  xmlns:prezent="prezent"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    ...

        <field name="locale" column="locale" type="string" length="2">
            <prezent:locale />
        </field>

        <!-- field="translatable" #optional (default: translatable) -->
        <!-- target-entity="BlogPost" # optional (default: entity name without "Translation" suffix) -->
        <prezent:translatable field="translatable" target-entity="BlogPost"/>

    ...

</doctrine-mapping>
```
