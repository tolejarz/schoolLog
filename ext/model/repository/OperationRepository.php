<?php
class OperationRepository extends Repository {
    protected $table_name = 'operations';
    protected $fields = array(
        'id' => array('type' => '%i', 'nullable' => false, 'primary' => true),
        'date_creation'         => array('type' => '%s', 'nullable' => false),
        'date_origine'          => array('type' => '%s', 'nullable' => true),
        'date_report'           => array('type' => '%s', 'nullable' => true),
        'etat'                  => array('type' => '%s', 'nullable' => false),
        'id_enseignant'         => array('type' => '%i', 'nullable' => false),
        'id_modele_planning'    => array('type' => '%i', 'nullable' => false),
    );
    
    public function get($key, $value, $options) {
        $options['query'] = '
            select
                operations.*,
                matieres.nom as nom_matiere,
                classes.libelle as nom_classe,
                classes.email as email_classe,
                utilisateurs.email as email_enseignant
            from enseignants_matieres_classes
            left join matieres on matieres.id=enseignants_matieres_classes.id_matiere
            left join classes on classes.id=enseignants_matieres_classes.id_classe
            left join utilisateurs on utilisateurs.id=operations.id_enseignant
        ';
        return parent::get($key, $value, $options);
    }
    
    public function search($filters = array(), $options = array()) {
        $options['query'] = 'select 
                classes.libelle as classe,
                matieres.nom as matiere,
                operations.date_origine,
                modele_planning.heure_debut,
                operations.date_report,
                operations.etat,
                operations.id,
                concat(utilisateurs.civility, " ", utilisateurs.nom) as enseignant
            from
                operations
            left join modele_planning on modele_planning.id=operations.id_modele_planning
            left join classes on classes.id=modele_planning.id_classe
            left join matieres on matieres.id=modele_planning.id_matiere
            left join utilisateurs on utilisateurs.id=operations.id_enseignant

            where
                (operations.date_report is null or operations.date_report>now())
                ' . (!empty($i['id_enseignant']) ? 'and ' . $i['id_enseignant'] . ' in (
                    select emc.id_enseignant
                    from enseignants_matieres_classes emc
                    where emc.id_matiere=mp.id_matiere
                    and emc.id_classe=mp.id_classe
                )' : '') . '
            order by
                operations.date_creation desc
        ';
        return parent::search($filters, $options);
    }
}
?>
