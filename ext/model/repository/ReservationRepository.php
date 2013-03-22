<?php
class ReservationRepository extends Repository {
    protected $table_name = 'reservations';
    protected $fields = array(
        'id'                => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'date_creation'     => array('type' => '%s', 'nullable' => false),
        'date_heure_debut'  => array('type' => '%s', 'nullable' => true),
        'date_heure_fin'    => array('type' => '%s', 'nullable' => true),
        'id_enseignant'     => array('type' => '%i', 'nullable' => true),
        'id_materiel'       => array('type' => '%i', 'nullable' => true),
        'etat'              => array('type' => '%s', 'nullable' => true),
    );
    
    public function get($key, $value, $options) {
        /*
        date_format(reservations.date_heure_debut, "%w") as jour,
                case date_format(reservations.date_heure_debut, "%w")
                    when 1 then "lundi"
                    when 2 then "mardi"
                    when 3 then "mercredi"
                    when 4 then "jeudi"
                    when 5 then "vendredi"
                end as jour_libelle,
                unix_timestamp(reservations.date_heure_debut) as date_heure_debut,
                time(reservations.date_heure_debut) as heure_debut
                addtime(time(reservations.date_heure_debut), timediff(reservations.date_heure_fin, reservations.date_heure_debut)) as heure_fin,
        */
        $options['query'] = '
            select 
                reservations.id,
                reservations.date_heure_debut,
                reservations.date_heure_fin,
                reservations.etat,
                materiels.etat as etat_materiel,
                concat(utilisateurs.civility, " ", utilisateurs.nom) as enseignant,
                utilisateurs.id as id_enseignant
            from
                reservations
            left join materiels on materiels.id=reservations.id_materiel
            left join utilisateurs on utilisateurs.id=reservations.id_enseignant';
        return parent::get($key, $value, $options);
        /*
            where
                date_format(reservations.date_heure_debut, "%u")=' . $value['week'] . '
                and reservations.id_materiel=' . $value['id_materiel']
        ;
        $r = $this->link->fetchOne($query, array());
        return $r;
    }
    
    public function search($filters = array(), $options = array()) {
        $options['query'] = 'select
            matieres.id as id_matiere,
            matieres.nom as nom_matiere,
            
            classes.id as id_classe,
            classes.libelle as nom_classe,
            
            utilisateurs.id as id_enseignant,
            utilisateurs.nom as nom_enseignant
            
            from ' . $this->table_name . '
            left join matieres on matieres.id=' . $this->table_name . '.id_matiere
            left join classes on classes.id=' . $this->table_name . '.id_classe
            left join utilisateurs on utilisateurs.id=' . $this->table_name . '.id_enseignant
        ';
        return parent::search($filters, $options);
    }
}
?>
