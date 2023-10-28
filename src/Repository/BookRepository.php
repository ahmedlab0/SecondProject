<?php

namespace App\Repository;
use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function findByReference($ref)
    {
        return $this->createQueryBuilder('selim')
            ->Where('selim.ref=:ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();
    }


    public function ShowBookOrderByAuthor()
    {
        return $this->createQueryBuilder('selim')
        ->join('selim.author', 'author')
        ->addSelect('author')
            ->orderBy('author.username', 'Asc')
            ->getQuery()
            ->getResult();
    }

    public function findBooksPublishedBefore2023WithAuthorsMoreThan35Books() //ahawka esm twil ala khtrk mr
    {
        return $this->createQueryBuilder('book')
        ->join('book.author', 'author')
        ->Where('author.nb_books > 35')
        ->andWhere('book.publicationDate < :date')
        ->groupBy('author.nb_books')
        ->setParameter('date', new \DateTime('2023-01-01'))
        ->getQuery()
        ->getResult();
    }

    public function updateCategoryForWilliamShakespeareBooks()
    {
        return $this->createQueryBuilder('book')
            ->innerJoin('book.author', 'author')
            ->where('author.username = :authorName')
            ->setParameter('authorName', 'William Shakespeare')
            ->getQuery()
            ->getResult();
    }

    public function countBooksInScienceFictionCategory()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT COUNT(book.ref) as total
             FROM App\Entity\Book book
             WHERE book.category = :category'
        );
        $query->setParameter('category', 'Science-Fiction');
        return $query->getSingleScalarResult();
    }


    public function findBooksPublishedBetweenDates()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT book
             FROM App\Entity\Book book
             WHERE book.publicationDate between :startDate and :endDate'
        );
        $query->setParameter('startDate', '2014-01-01');
        $query->setParameter('endDate', '2018-12-31');
        return $query->getResult();
    }



    public function findBooksByAuthorBookCountRange($min, $max)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT book
            FROM App\Entity\Book book
            JOIN book.author author
            where author.nb_books between :min  and :max
            GROUP BY author.nb_books
        ');

$query->setParameter('min', $min);
$query ->setParameter('max', $max);
          return  $query->getResult();
    }


    public function DeleteAuthorWith0Books()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('DELETE FROM App\Entity\Author author
        WHERE NOT EXISTS (
            SELECT book
            FROM App\Entity\Book book
            WHERE author.nb_books = 0
        ');
         return  $query->getResult();
    }

}
