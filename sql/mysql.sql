# --------------------------------------------------------
#
# Table structure for table `todo`
#
CREATE TABLE tb_tabs (
    id         INT(5)      NOT NULL AUTO_INCREMENT,
    name       VARCHAR(30) NOT NULL,
    link       VARCHAR(100),
    activ_cond VARCHAR(100),
    parent_id  INT(5) DEFAULT 0,
    tb_ord     INT(5) DEFAULT 0,
    PRIMARY KEY (id)
)
    ENGINE = ISAM;
