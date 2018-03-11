<?php
// src/AppBundle/Entity/Article.php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


class Article
{
    private $title;
    private $category;
    private $content;
    private $publised_date;
    private $created_date;
}