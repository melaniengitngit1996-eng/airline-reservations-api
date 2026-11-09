<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261030034753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE airports (
            id INT AUTO_INCREMENT NOT NULL,
            code VARCHAR(3) NOT NULL,
            name VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            country VARCHAR(100) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_AIRPORT_CODE (code),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE flights (
            id INT AUTO_INCREMENT NOT NULL,
            flight_number VARCHAR(20) NOT NULL,
            capacity INT NOT NULL,
            departure_time DATETIME NOT NULL,
            arrival_time DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            origin_airport_id INT NOT NULL,
            destination_airport_id INT NOT NULL,
            INDEX IDX_FLIGHT_ORIGIN (origin_airport_id),
            INDEX IDX_FLIGHT_DESTINATION (destination_airport_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE seats (
            id INT AUTO_INCREMENT NOT NULL,
            flight_id INT NOT NULL,
            code VARCHAR(10) NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_SEAT_FLIGHT_CODE (flight_id, code),
            INDEX IDX_SEATS_FLIGHT (flight_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE passengers (
            id INT AUTO_INCREMENT NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE reservations (
            id INT AUTO_INCREMENT NOT NULL,
            created_at DATETIME NOT NULL,
            flight_id INT NOT NULL,
            INDEX IDX_RESERVATION_FLIGHT (flight_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE reservation_passengers (
            id INT AUTO_INCREMENT NOT NULL,
            reservation_id INT NOT NULL,
            passenger_id INT NOT NULL,
            seat_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX IDX_RP_RESERVATION (reservation_id),
            INDEX IDX_RP_PASSENGER (passenger_id),
            INDEX IDX_RP_SEAT (seat_id),
            UNIQUE INDEX UNIQ_RP_RESERVATION_PASSENGER (reservation_id, passenger_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE flights ADD CONSTRAINT FK_FLIGHT_ORIGIN FOREIGN KEY (origin_airport_id) REFERENCES airports (id)');
        $this->addSql('ALTER TABLE flights ADD CONSTRAINT FK_FLIGHT_DESTINATION FOREIGN KEY (destination_airport_id) REFERENCES airports (id)');
        $this->addSql('ALTER TABLE seats ADD CONSTRAINT FK_SEATS_FLIGHT FOREIGN KEY (flight_id) REFERENCES flights (id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_RESERVATION_FLIGHT FOREIGN KEY (flight_id) REFERENCES flights (id)');
        $this->addSql('ALTER TABLE reservation_passengers ADD CONSTRAINT FK_RP_RESERVATION FOREIGN KEY (reservation_id) REFERENCES reservations (id)');
        $this->addSql('ALTER TABLE reservation_passengers ADD CONSTRAINT FK_RP_PASSENGER FOREIGN KEY (passenger_id) REFERENCES passengers (id)');
        $this->addSql('ALTER TABLE reservation_passengers ADD CONSTRAINT FK_RP_SEAT FOREIGN KEY (seat_id) REFERENCES seats (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign keys first
        $this->addSql('ALTER TABLE reservation_passengers DROP FOREIGN KEY FK_RP_RESERVATION');
        $this->addSql('ALTER TABLE reservation_passengers DROP FOREIGN KEY FK_RP_PASSENGER');
        $this->addSql('ALTER TABLE reservation_passengers DROP FOREIGN KEY FK_RP_SEAT');

        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_RESERVATION_FLIGHT');
        $this->addSql('ALTER TABLE seats DROP FOREIGN KEY FK_SEATS_FLIGHT');

        $this->addSql('ALTER TABLE flights DROP FOREIGN KEY FK_FLIGHT_ORIGIN');
        $this->addSql('ALTER TABLE flights DROP FOREIGN KEY FK_FLIGHT_DESTINATION');

        // Drop tables in reverse dependency order
        $this->addSql('DROP TABLE reservation_passengers');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE passengers');
        $this->addSql('DROP TABLE seats');
        $this->addSql('DROP TABLE flights');
        $this->addSql('DROP TABLE airports');
    }
}
