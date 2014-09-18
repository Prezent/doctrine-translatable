Usage
=====

## Using translations

If you followed the [example](getting-started.md) then translations will be automatically
loaded from the database for the current locale configured in the listener.

```php

$translatableListener->setCurrentLocale('en');

$post = $em->find('BlogPost', 1);
$post->getTitle(); // Title in English
```

To access translations in different locales, simply get them from the relation.
Translations are indexed by locale.

```php
// Direct access
$post->getTranslations()->get('de')->getTitle(); // Title in German

// Via the translate() helper from the example
$post->translate('fr')->getTitle(); // Title in French
```

## Querying translations

Translations are plain related entities. You can query them directly.

```php
// Search posts by title
$qb = $em->createQueryBuilder();
$qb->select('p')
   ->from('BlogPost', p)
   ->join('p.translations', 'pt', 'pt.locale = :locale')
   ->where('pt.title = :title');

$posts = $qb->getQuery()
    ->setParameters(array(
        'locale' => 'nl',
        'title'  => 'Dutch title',
    ))
    ->getResult();
```

Normally when you call getTranslations() or translate() on an entity, the database is queried. But
you can fetch-load translations just like any other entity:

```php
// Fetch-load a single locale
$qb = $em->createQueryBuilder();
$qb->select('p', 'pt')
   ->from('BlogPost', p)
   ->join('p.translations', 'pt', 'pt.locale = :locale');

$posts = $qb->getQuery()
    ->setParameter('locale', 'en')
    ->getResult();

// Fetch-load all locales
$qb = $em->createQueryBuilder();
$qb->select('p', 'pt')
   ->from('BlogPost', p)
   ->join('p.translations', 'pt');

$posts = $qb->getQuery()->getResult();
```

## Fallback translations

You can implement fallback translations yourself in your entity models. To enable this, you can create
a property tagged with the `FallbackLocale` annotation. This property will be filled with the fallback locale
configured in the translatable listener. Example:

```php

// BlogPost.php

class BlogPost extends AbstractTranslatable
{
    // Mappings
    // ...

    /**
     * @Prezent\FallbackLocale
     */
    private $fallbackLocale;

    /**
     * Translation helper method that uses a fallback locale
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
            if (!$translation = $this->translations->get($this->fallbackLocale)) {
                throw new \RuntimeException('No translation in current or fallback locale');
            }
        }

        $this->currentTranslation = $translation;
        return $translation;
    }
}

// Your controller

$translatableListener
    ->setCurrentLocale('fr')
    ->setFallbackLocale('en');

$post = $em->find('BlogPost', 1);
$post->getTitle(); // Title in French, or English if French is not available
```
