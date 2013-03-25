<h2>Liste des matières de la classe <?php echo $parms['classe']; ?></h2>
<a class="addLink" href="<?php echo Router::build('ClassSubjectAdd', array('class_id' => $parms['id_classe'])); ?>" target="_self">Ajouter une matière</a>
<p class="separator3"></p>
<?php if (!empty($parms['subjects'])) : ?>
    <table class="listingContainer">
        <tr>
            <th id="actions">Actions</th>
            <th>Matière</th>
            <th>Enseignant(s)</th>
        </tr>
    <?php foreach ($parms['subjects'] as $subject) : ?>
        <tr>
            <td class="centered">
                <a href="<?php echo Router::build('ClassSubjectDelete', array('class_id' => $parms['id_classe']), array('id_matiere' => $subject['id_matiere'])); ?>" target="_self"><img alt="Supprimer" src="/img/delete.png" title="Supprimer" /></a>
                <a href="<?php echo Router::build('ClassSubjectEdit', array('class_id' => $parms['id_classe']), array('id_matiere' => $subject['id_matiere'])); ?>" target="_self"><img alt="Modifier" src="/img/edit.png" title="Modifier" /></a></td>
            <td><?php echo $subject['nom_matiere']; ?></td>
            <td>
            <?php if ($subject['enseignants'] == null) : ?>
                aucun
            <?php else : ?>
                <?php foreach ($subject['enseignants'] as $e) : ?>
                <p class="thinParagraph"><?php echo $e['civility']; ?> <?php echo $e['nom']; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php else : ?>
    <p class="notice">Aucune matière</p>
<?php endif; ?>
