
CREATE TABLE `buchungen` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,

    `jahr` SMALLINT NOT NULL,
    `monat` TINYINT NOT NULL,
    `tag` TINYINT NOT NULL,
    `blocknummer` TINYINT NOT NULL,
    `slotnummer` TINYINT NOT NULL,

    `name` VARCHAR(255) NULL,
    `telefonnummer` VARCHAR(255) NULL,
	`zentner` TINYINT NULL,
    `obstsorte` VARCHAR(1) NULL,

    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
ALTER TABLE `buchungen` ADD UNIQUE `zeitindex` (`jahr`, `monat`, `tag`, `blocknummer`, `slotnummer`);

CREATE TABLE `variablen` (
    `name` VARCHAR(255) NOT NULL,
    `wert` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`name`)
) ENGINE = InnoDB;

CREATE TABLE `freigeschaltete_tage` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,

    `jahr` SMALLINT NOT NULL,
    `monat` TINYINT NOT NULL,
    `tag` TINYINT NOT NULL,

    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
ALTER TABLE `freigeschaltete_tage` ADD UNIQUE `tagindex` (`jahr`, `monat`, `tag`);
