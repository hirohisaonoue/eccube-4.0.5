<?php

/*
 * Project Name: 商品情報項目追加プラグイン for 4.0
 * Copyright(c) 2018 Kenji Nakanishi. All Rights Reserved.
 *
 * https://www.facebook.com/web.kenji.nakanishi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SheebExpandProductColumn;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Application;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Repository\CsvRepository;
use Plugin\SheebExpandProductColumn\Entity\ColumnValue;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class PluginManager extends AbstractPluginManager
{
    /**
     * dtb_csv テーブルに追加した値を削除します
     * 
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function uninstall(array $meta, ContainerInterface $container)
    {
        $canFlush = false;
        /**
         * @var $em EntityManagerInterface
         */
        $em = $container->get('doctrine.orm.entity_manager');
        
        $columnValueRepository = $em->getRepository(ColumnValue::class);
        $columnRepository = $em->getRepository(ProductColumn::class);

        $columnValues = $columnValueRepository->findAll();
        $columns = $columnRepository->findAll();
        
        // ProductColumnValueの削除

        if (!empty($columnValues)) {
            $canFlush = true;
            foreach ($columnValues as $record) {
                $em->remove($record);
            }
        }
        
        if (!empty($columns)) {
            // CSVテーブルに追加していたデータの削除
            $csv_list = array_reduce($columns, function ($reduced, ProductColumn $ProductColumn) {
                $reduced[] = $ProductColumn->getCsv();
                return $reduced;
            }, []);
            if (!empty($csv_list)) {
                $canFlush = true;
                foreach ($csv_list as $csv) {
                    $em->remove($csv);
                }
            }

            // ProductColumnの削除
            if (!empty($columns)) {
                $canFlush = true;
                foreach ($columns as $record) {
                    $em->remove($record);
                }
            }
        }

        if ($canFlush) {
            $em->flush();
        }
    }

    /**
     * 最初にSEOメタタグ用フィールドを自動追加します
     * 
     * @param array $meta
     * @param ContainerInterface $container
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function enable(array $meta, ContainerInterface $container)
    {
        /**
         * @var $em EntityManagerInterface
         */
        $em = $container->get('doctrine.orm.entity_manager');

        // すでに登録済みなら何もしない
        $columnRepository = $em->getRepository(ProductColumn::class);
        $ProductColumns = $columnRepository->findByCode([
            'description', 'keywords', 'og_app_id', 'og_image'
        ]);
        if (!empty($ProductColumns)) {
            return;
        }

        $entities = [];
        /**
         * @var CsvType $CsvType
         */
        $CsvType = $em->getRepository(CsvType::class)->find(CsvType::CSV_TYPE_PRODUCT);

        // description
        $description = new ProductColumn();
        $description->setCode('description');
        $description->setName('ディスクリプション');
        $description->setType('textarea');
        $description->setIsMultiple(false);
        $description->setIsSearchable(true);
        $em->persist($description);
        $entities[] = $description;

        // keywords
        $keywords = new ProductColumn();
        $keywords->setCode('keywords');
        $keywords->setName('キーワード');
        $keywords->setType('text');
        $keywords->setIsMultiple(false);
        $keywords->setIsSearchable(true);
        $em->persist($keywords);
        $entities[] = $keywords;

        // og_app_id
        $ogAppId = new ProductColumn();
        $ogAppId->setCode('og_app_id');
        $ogAppId->setName('Facebook APP ID');
        $ogAppId->setType('text');
        $ogAppId->setIsMultiple(false);
        $ogAppId->setIsSearchable(false);
        $em->persist($ogAppId);
        $entities[] = $ogAppId;


        // og_image
        $ogImage = new ProductColumn();
        $ogImage->setCode('og_image');
        $ogImage->setName('OG 画像');
        $ogImage->setType('images');
        $ogImage->setIsMultiple(false);
        $ogImage->setIsSearchable(false);
        $em->persist($ogImage);
        $entities[] = $ogImage;

        // ProductColumn の ID を確定させるために flush
        $em->flush($entities);
        
        // Csv データを追加
        $descriptionCsv = $this->createCsv($em, $description, $CsvType);
        $description->setCsv($descriptionCsv);
        $entities[] = $descriptionCsv;

        $keywordsCsv = $this->createCsv($em, $keywords, $CsvType);
        $keywords->setCsv($keywordsCsv);
        $entities[] = $keywordsCsv;

        $ogAppIdCsv = $this->createCsv($em, $ogAppId, $CsvType);
        $ogAppId->setCsv($ogAppIdCsv);
        $entities[] = $ogAppIdCsv;
        
        $ogImageCsv = $this->createCsv($em, $ogImage, $CsvType);
        $ogImage->setCsv($ogImageCsv);
        $entities[] = $ogImageCsv;

        // 再保存
        $em->flush($entities);
    }

    /**
     * @param EntityManagerInterface $em
     * @param ProductColumn $ProductColumn
     * @param CsvType $CsvType
     * @return Csv
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function createCsv(EntityManagerInterface $em, ProductColumn $ProductColumn, CsvType $CsvType)
    {
        /**
         * @var CsvRepository $csvRepository
         */
        $csvRepository = $em->getRepository(Csv::class);
        $sortNo = $csvRepository->createQueryBuilder('c')
            ->select('COALESCE(MAX(c.sort_no), 0)')
            ->getQuery()
            ->getSingleScalarResult();
        
        $Csv = new Csv();
        $Csv->setCsvType($CsvType)
            ->setEntityName(ColumnValue::class)
            ->setFieldName('value')
            ->setReferenceFieldName($ProductColumn->getId())
            ->setDispName($ProductColumn->getName())
            ->setSortNo($sortNo);
        $em->persist($Csv);
        
        return $Csv;
    } 

    

    public static function dumpToLog($object, $mark = '')
    {
        $target = __DIR__ . '/plugin.log';
        if (!empty($mark)) {
            file_put_contents($target, $mark, FILE_APPEND);
        }
        
        $cloner = new VarCloner();
        $dumper = new CliDumper();

        $dumper->dump(
            $cloner->cloneVar($object),
            function ($line, $depth) use ($target) {
                // A negative depth means "end of dump"
                if ($depth >= 0) {
                    // Adds a two spaces indentation to the line
                    $output = str_repeat('  ', $depth).$line."\n";
                    file_put_contents($target, $output, FILE_APPEND);
                }
            }
        );
    }

}
