<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261010132452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE additional_info_field (id INT AUTO_INCREMENT NOT NULL, field_name VARCHAR(255) NOT NULL, type INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bon_commande (id INT AUTO_INCREMENT NOT NULL, fournisseur_id INT NOT NULL, order_date DATETIME NOT NULL, is_fully_received TINYINT(1) DEFAULT NULL, total_amount DOUBLE PRECISION NOT NULL, final_delivery_date DATE DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, order_number BIGINT NOT NULL, INDEX IDX_159D9576670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE details_bon_commande (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, bon_commande_id INT DEFAULT NULL, quantity INT NOT NULL, remaining_not_arrived_stock INT DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, INDEX IDX_38F400D1F347EFB (produit_id), INDEX IDX_38F400D1B4B54061 (bon_commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile_role (profile_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_E1A105FECCFA12B8 (profile_id), INDEX IDX_E1A105FED60322AC (role_id), PRIMARY KEY(profile_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reception_bon_commande (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, details_bon_commande_id INT NOT NULL, date DATETIME NOT NULL, quantity INT NOT NULL, INDEX IDX_7D52D55F4584665A (product_id), INDEX IDX_7D52D55F50DAAC0A (details_bon_commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, groupe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, ware_house_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_4B3656604584665A (product_id), INDEX IDX_4B36566049FA2CE4 (ware_house_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_history (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_movement (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, destination_id INT NOT NULL, origin_id INT DEFAULT NULL, reception_bon_commande_id INT DEFAULT NULL, quantity INT NOT NULL, date DATETIME NOT NULL, remaining_not_arrived_stock INT NOT NULL, is_finished TINYINT(1) NOT NULL, INDEX IDX_BB1BC1B54584665A (product_id), INDEX IDX_BB1BC1B5816C6140 (destination_id), INDEX IDX_BB1BC1B556A273CC (origin_id), UNIQUE INDEX UNIQ_BB1BC1B5788BAD42 (reception_bon_commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tiers (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, raison_sociale VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone INT NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_16473BA2C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tiers_type (id INT AUTO_INCREMENT NOT NULL, name_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, name VARCHAR(255) NOT NULL, conversion_rate INT NOT NULL, INDEX IDX_DCBB0C534584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, profile_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone INT NOT NULL, image LONGTEXT DEFAULT NULL, INDEX IDX_8D93D649CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE value_additional_info (id INT AUTO_INCREMENT NOT NULL, additional_info_field_id INT NOT NULL, tiers_id INT DEFAULT NULL, order_item_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_8076AE8590721E34 (additional_info_field_id), INDEX IDX_8076AE8568B77723 (tiers_id), INDEX IDX_8076AE85E415FB15 (order_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ware_house (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, address_line VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, phone INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_reception (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, stock_movement_id INT NOT NULL, date DATETIME NOT NULL, quantity INT NOT NULL, INDEX IDX_6F43CB904584665A (product_id), INDEX IDX_6F43CB90FD50D693 (stock_movement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bon_commande ADD CONSTRAINT FK_159D9576670C757F FOREIGN KEY (fournisseur_id) REFERENCES tiers (id)');
        $this->addSql('ALTER TABLE details_bon_commande ADD CONSTRAINT FK_38F400D1F347EFB FOREIGN KEY (produit_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE details_bon_commande ADD CONSTRAINT FK_38F400D1B4B54061 FOREIGN KEY (bon_commande_id) REFERENCES bon_commande (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE profile_role ADD CONSTRAINT FK_E1A105FECCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_role ADD CONSTRAINT FK_E1A105FED60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reception_bon_commande ADD CONSTRAINT FK_7D52D55F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE reception_bon_commande ADD CONSTRAINT FK_7D52D55F50DAAC0A FOREIGN KEY (details_bon_commande_id) REFERENCES details_bon_commande (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566049FA2CE4 FOREIGN KEY (ware_house_id) REFERENCES ware_house (id)');
        $this->addSql('ALTER TABLE stock_movement ADD CONSTRAINT FK_BB1BC1B54584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock_movement ADD CONSTRAINT FK_BB1BC1B5816C6140 FOREIGN KEY (destination_id) REFERENCES ware_house (id)');
        $this->addSql('ALTER TABLE stock_movement ADD CONSTRAINT FK_BB1BC1B556A273CC FOREIGN KEY (origin_id) REFERENCES ware_house (id)');
        $this->addSql('ALTER TABLE stock_movement ADD CONSTRAINT FK_BB1BC1B5788BAD42 FOREIGN KEY (reception_bon_commande_id) REFERENCES reception_bon_commande (id)');
        $this->addSql('ALTER TABLE tiers ADD CONSTRAINT FK_16473BA2C54C8C93 FOREIGN KEY (type_id) REFERENCES tiers_type (id)');
        $this->addSql('ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C534584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE value_additional_info ADD CONSTRAINT FK_8076AE8590721E34 FOREIGN KEY (additional_info_field_id) REFERENCES additional_info_field (id)');
        $this->addSql('ALTER TABLE value_additional_info ADD CONSTRAINT FK_8076AE8568B77723 FOREIGN KEY (tiers_id) REFERENCES tiers (id)');
        $this->addSql('ALTER TABLE value_additional_info ADD CONSTRAINT FK_8076AE85E415FB15 FOREIGN KEY (order_item_id) REFERENCES details_bon_commande (id)');
        $this->addSql('ALTER TABLE warehouse_reception ADD CONSTRAINT FK_6F43CB904584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE warehouse_reception ADD CONSTRAINT FK_6F43CB90FD50D693 FOREIGN KEY (stock_movement_id) REFERENCES stock_movement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bon_commande DROP FOREIGN KEY FK_159D9576670C757F');
        $this->addSql('ALTER TABLE details_bon_commande DROP FOREIGN KEY FK_38F400D1F347EFB');
        $this->addSql('ALTER TABLE details_bon_commande DROP FOREIGN KEY FK_38F400D1B4B54061');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE profile_role DROP FOREIGN KEY FK_E1A105FECCFA12B8');
        $this->addSql('ALTER TABLE profile_role DROP FOREIGN KEY FK_E1A105FED60322AC');
        $this->addSql('ALTER TABLE reception_bon_commande DROP FOREIGN KEY FK_7D52D55F4584665A');
        $this->addSql('ALTER TABLE reception_bon_commande DROP FOREIGN KEY FK_7D52D55F50DAAC0A');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604584665A');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566049FA2CE4');
        $this->addSql('ALTER TABLE stock_movement DROP FOREIGN KEY FK_BB1BC1B54584665A');
        $this->addSql('ALTER TABLE stock_movement DROP FOREIGN KEY FK_BB1BC1B5816C6140');
        $this->addSql('ALTER TABLE stock_movement DROP FOREIGN KEY FK_BB1BC1B556A273CC');
        $this->addSql('ALTER TABLE stock_movement DROP FOREIGN KEY FK_BB1BC1B5788BAD42');
        $this->addSql('ALTER TABLE tiers DROP FOREIGN KEY FK_16473BA2C54C8C93');
        $this->addSql('ALTER TABLE unit DROP FOREIGN KEY FK_DCBB0C534584665A');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649CCFA12B8');
        $this->addSql('ALTER TABLE value_additional_info DROP FOREIGN KEY FK_8076AE8590721E34');
        $this->addSql('ALTER TABLE value_additional_info DROP FOREIGN KEY FK_8076AE8568B77723');
        $this->addSql('ALTER TABLE value_additional_info DROP FOREIGN KEY FK_8076AE85E415FB15');
        $this->addSql('ALTER TABLE warehouse_reception DROP FOREIGN KEY FK_6F43CB904584665A');
        $this->addSql('ALTER TABLE warehouse_reception DROP FOREIGN KEY FK_6F43CB90FD50D693');
        $this->addSql('DROP TABLE additional_info_field');
        $this->addSql('DROP TABLE bon_commande');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE details_bon_commande');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE profile_role');
        $this->addSql('DROP TABLE reception_bon_commande');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE stock_history');
        $this->addSql('DROP TABLE stock_movement');
        $this->addSql('DROP TABLE tiers');
        $this->addSql('DROP TABLE tiers_type');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE value_additional_info');
        $this->addSql('DROP TABLE ware_house');
        $this->addSql('DROP TABLE warehouse_reception');
    }
}
