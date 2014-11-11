Getting started
===============

Let's assume you have a blog post entity that you want to translate. Here is what your
entity would currently look like:

```php
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 */
class BlogPost
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="identity")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="blogPosts")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $title = "";

    /**
     * @ORM\Column(type="text")
     */
    private $content = "";

    // Getters and setters
}
```

## Updating your entities and mappings

If you want to translate the `title` and `content` fields, then you have to create a new translation entity
and move those fields there. Then add the appropriate translatable mappings. For this example we will
use the abstract base classes provided with this extension. You can also use the traits, or implement
the interfaces yourself. Here is how the entities would end up looking:

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * @Entity
 */
class BlogPost extends AbstractTranslatable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="identity")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @Prezent\Translations(targetEntity="BlogPostTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="blogPosts")
     */
    private $user;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    // Getters and setters
}
```

Things to note:

* The `id` property is now protected instead of private, because it is defined in the
  abstract base class.
* The `translations` property is a one-to-many relation to the translation class. You can map
  it manually if you want, but by using the `Translation` mapping, it will be mapped automatically.
  Because it is a one-to-many relation, you have to initialize it as an ArrayCollection in the
  constructor.
* The `currentLocale` field will be filled by this extension with the code of the current locale.

```php
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Entity
 */
class BlogPostTranslation extends AbstractTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="BlogPost")
     */
    protected $translatable;

    /**
     * @ORM\Column(type="string")
     */
    private $title = "";

    /**
     * @ORM\Column(type="text")
     */
    private $content = "";

    // Getters and setters
}
```

Things to note:

* The `title` and `content` fields have been moved from the BlogPost entity to the BlogPostTranslation entity
* The `translatable` property can be mapped manually, but using the `Translatable` annotation means it will be
  mapped automatically by this extension.

## Proxy getters and setters

You probably do not want to change the API of your BlogPost entity. Therefor you should provide proxy getters and setters
for the properties that have moved to the translation class. The Translatable extension does not dictate how you should do
this. The way you want to deal with translations and fallbacks for missing translations depends very much on your
own domain logic. However, here is one suggested way of handling them.


```php
class BlogPost extends AbstractTranslatable
{
    // Mappings, as above
    // ...

    private $currentTranslation; // Cache current translation. Useful in Doctrine 2.4+

    /**
     * Translation helper method
     */
    public function translate($locale = null)
    {
        if (null === $locale) {
            $locale = $this->currentLocale;
        }

        if (!$locale) {
            throw new \RuntimeException('No locale has been set and currentLocale is empty');
        }

        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }

        if (!$translation = $this->translations->get($locale)) {
            $translation = new BlogPostTranslation();
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;
        return $translation;
    }

    // Proxy getters and setters

    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    public function setTitle($title)
    {
        $this->translate()->setTitle($title);
        return $this;
    }

    public function getContent()
    {
        return $this->translate()->getContent();
    }

    public function setContent($content)
    {
        $this->translate()->setContent($content);
        return $this;
    }
}
```

In this example, when a translation is not found in a certain locale, it will be created automatically
by the `translate()` helper method. In your domain logic it might make more sense to simply throw an
exception when a translation is missing, or fall back to a different locale. This is entirely up to you.
Of course, you could put this helper method in an abstract base class or trait of your own, so you can share
it with multiple entities.

## Setup the translatable listener

The last thing you need to to is setup the translatable listener. If you use Symfony2, you should use the
[prezent/doctrine-translatable-bundle](https://github.com/Prezent/doctrine-translatable-bundle/blob/master/Resources/doc/index.md)
which does this for you.

First, create the listener instance. It needs a metadata factory in order to read the custom annotations. There is a `DoctrineAdapter`
that can automatically create the correct drivers based on your Doctrine configuration.

```php
use Metadata\MetadataFactory;
use Prezent\Doctrine\Translatable\Mapping\Driver\DoctrineAdapter;
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener;

$driver = DoctrineAdapter::fromManager($em);
$metadataFactory  = new MetadataFactory($driver);
$translatableListener = new TranslatableListener($metadataFactory);
```

Register the listener with your EntityManager:

```php
$em->getEventManager()->addEventSubscriber($translatableListener);
```

And finally, register the new annotations with the autoloader.

```php
AnnotationRegistry::registerAutoloadNamespace('Prezent\\Doctrine\\Translatable\\Annotation', 'path/to/doctrine-translatable/lib');
```
