<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217085816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__commentaire AS SELECT id, recette_id, texte, date FROM commentaire');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('CREATE TABLE commentaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recette_id INTEGER NOT NULL, utilisateur_id INTEGER NOT NULL, texte CLOB NOT NULL, date DATETIME NOT NULL, CONSTRAINT FK_67F068BC89312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67F068BCFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO commentaire (id, recette_id, texte, date) SELECT id, recette_id, texte, date FROM __temp__commentaire');
        $this->addSql('DROP TABLE __temp__commentaire');
        $this->addSql('CREATE INDEX IDX_67F068BC89312FE9 ON commentaire (recette_id)');
        $this->addSql('CREATE INDEX IDX_67F068BCFB88E14F ON commentaire (utilisateur_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recette_ingredient AS SELECT id, recette_id, ingredient_id, quantite FROM recette_ingredient');
        $this->addSql('DROP TABLE recette_ingredient');
        $this->addSql('CREATE TABLE recette_ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recette_id INTEGER NOT NULL, ingredient_id INTEGER NOT NULL, quantite INTEGER NOT NULL, CONSTRAINT FK_17C041A989312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_17C041A9933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO recette_ingredient (id, recette_id, ingredient_id, quantite) SELECT id, recette_id, ingredient_id, quantite FROM __temp__recette_ingredient');
        $this->addSql('DROP TABLE __temp__recette_ingredient');
        $this->addSql('CREATE INDEX IDX_17C041A989312FE9 ON recette_ingredient (recette_id)');
        $this->addSql('CREATE INDEX IDX_17C041A9933FE08C ON recette_ingredient (ingredient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__commentaire AS SELECT id, recette_id, texte, date FROM commentaire');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('CREATE TABLE commentaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recette_id INTEGER NOT NULL, texte CLOB NOT NULL, date DATETIME NOT NULL, CONSTRAINT FK_67F068BC89312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO commentaire (id, recette_id, texte, date) SELECT id, recette_id, texte, date FROM __temp__commentaire');
        $this->addSql('DROP TABLE __temp__commentaire');
        $this->addSql('CREATE INDEX IDX_67F068BC89312FE9 ON commentaire (recette_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recette_ingredient AS SELECT id, recette_id, ingredient_id, quantite FROM recette_ingredient');
        $this->addSql('DROP TABLE recette_ingredient');
        $this->addSql('CREATE TABLE recette_ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recette_id INTEGER NOT NULL, ingredient_id INTEGER NOT NULL, quantite INTEGER NOT NULL, CONSTRAINT FK_17C041A989312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_17C041A9933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO recette_ingredient (id, recette_id, ingredient_id, quantite) SELECT id, recette_id, ingredient_id, quantite FROM __temp__recette_ingredient');
        $this->addSql('DROP TABLE __temp__recette_ingredient');
        $this->addSql('CREATE INDEX IDX_17C041A989312FE9 ON recette_ingredient (recette_id)');
        $this->addSql('CREATE INDEX IDX_17C041A9933FE08C ON recette_ingredient (ingredient_id)');
    }
}
