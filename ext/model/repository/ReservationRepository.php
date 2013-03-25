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
        $options['query'] = '
            select 
                reservations.id,
                reservations.date_heure_debut,
                reservations.date_heure_fin,
                reservations.etat,
                materiels.etat as etat_materiel,
                materiels.modele as materiel,
                concat(utilisateurs.civility, " ", utilisateurs.nom) as enseignant,
                utilisateurs.id as id_enseignant
            from
                reservations
            left join materiels on materiels.id=reservations.id_materiel
            left join utilisateurs on utilisateurs.id=reservations.id_enseignant';
        return parent::get($key, $value, $options);
    }
    
    public function search($filters = array(), $options = array()) {
        $options['query'] = '
            select 
                reservations.id,
                reservations.date_heure_debut,
                reservations.date_heure_fin,
                reservations.etat,
                materiels.etat as etat_materiel,
                concat(utilisateurs.civility, " ", utilisateurs.nom) as enseignant,
                utilisateurs.id as id_enseignant,
                utilisateurs.email as email_enseignant
            from
                reservations
            left join materiels on materiels.id=reservations.id_materiel
            left join utilisateurs on utilisateurs.id=reservations.id_enseignant';
        return parent::search($filters, $options);
    }
}
?>
