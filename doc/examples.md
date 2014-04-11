Complete examples
=================

## Using the abstract base classes

This example uses the abstract base classes for PHP 5.3. It also adds a `getTranslationEntityClass` method
for support with the [a2lix/TranslationsFormBundle](https://github.com/a2lix/TranslationFormBundle). On top
of this, it has a separate abstract base class for all your translatable entities.

```php
<?php
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

abstract class TranslatableEntity extends AbstractTranslatable
{
    /**
     * @Prezent\CurrentLocale
     */
    protected $currentLocale;

    /**
     * Cache current translation. Useful in Doctrine 2.4+
     */
    protected $currentTranslation;

    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    public function setCurrentLocale($locale)
    {
        $this->currentLocale = $locale;
        return $this;
    }

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
            $className=$this->getTranslationEntityClass();
            $translation = new $className;
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;
        return $translation;
    }

    /**
     * Used for a2lix translations and the translate helper
     * @return string
     */
    public function getTranslationEntityClass() {
        return get_class($this).'Translation';
    }

}
```

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * @Entity
 */
class BlogPost extends TranslatableEntity
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="blogPosts")
     */
    private $user;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content
        return $this;
    }

    public static function getTranslationEntityClass()
    {
        return 'BlogPostTranslation';
    }
}
```

## Using traits

This example is the same as before, except it uses the traits for PHP 5.4.

```php
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\TranslatableTrait;

trait MyTranslatableTrait
{
    use TranslatableTrait;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * Cache current translation. Useful in Doctrine 2.4+
     */
    private $currentTranslation;

    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    public function setCurrentLocale($locale)
    {
        $this->currentLocale = $locale;
        return $this;
    }

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
            $translation = new self::getTranslationEntityClass();
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;
        return $translation;
    }
}
```

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;

/**
 * @Entity
 */
class BlogPost implements TranslatableInterface
{
    use MyTranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="identity")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Prezent\Translations(targetEntity="BlogPostTranslation")
     */
    private $translations;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="blogPosts")
     */
    private $user;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

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

    public static function getTranslationEntityClass()
    {
        return 'BlogPostTranslation';
    }
}
```

```php
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\TranslationTrait;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Entity
 */
class BlogPostTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="locale", type="string")
     * @Prezent\Locale
     */
    private $locale;

    /**
     * @Prezent\Translatable(targetEntity="BlogPost")
     */
    private $translatable;

    /**
     * @ORM\Column(type="string")
     */
    private $title = "";

    /**
     * @ORM\Column(type="text")
     */
    private $content = "";

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content
        return $this;
    }

    public static function getTranslationEntityClass()
    {
        return 'BlogPostTranslation';
    }
}
```
