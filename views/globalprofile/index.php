<? if ($profile) : ?>

<form action="<?= PluginEngine::getLink($plugin, array(), $con."/edit") ?>"
      method="post"
      class="default evasys_presets">

    <div style="text-align: center;">
        <?= \Studip\Button::create(_("Speichern")) ?>
    </div>

    <fieldset>
        <legend>
            <? if ($this->controller->profile_type !== "institute") : ?>
                <?= _("Standarddaten der Evaluationen") ?>
            <? else : ?>
                <?= sprintf(_("Standarddaten der Evaluationen der Einrichtung %s"), htmlReady($profile->institute->name)) ?>
            <? endif ?>
        </legend>
        <label>
            <?= _("Beginn") ?>
            <input type="text" name="data[begin]" value="<?= $profile['begin'] ? date("d.m.Y H:i", $profile['begin']) : "" ?>" class="datepicker">
        </label>
        <? if ($this->controller->profile_type === "institute") : ?>
            <? $default_value = $profile->getParentsDefaultValue("begin") ?>
            <span title="<?= _("Standardwert, wenn nichts eingetragen ist.") ?>"
                  class="default_value">(<?= $default_value ? date("d.m.Y H:i", $default_value) : _("Kein Standardwert") ?>)</span>
        <? endif ?>

        <label>
            <?= _("Ende") ?>
            <input type="text" name="data[end]" value="<?= $profile['end'] ? date("d.m.Y H:i", $profile['end']) : "" ?>" class="datepicker">
        </label>
        <? if ($this->controller->profile_type === "institute") : ?>
            <? $default_value = $profile->getParentsDefaultValue("end") ?>
            <span title="<?= _("Standardwert, wenn nichts eingetragen ist.") ?>"
                  class="default_value">(<?= $default_value ? date("d.m.Y H:i", $default_value) : _("Kein Standardwert") ?>)</span>
        <? endif ?>

        <? if (is_a($profile, "EvasysGlobalProfile")) : ?>
            <label>
                <?= _("Beginn Berabeitungszeitraum der Admins") ?>
                <input type="text" name="data[adminedit_begin]" value="<?= $profile['adminedit_begin'] ? date("d.m.Y H:i", $profile['adminedit_begin']) : "" ?>" class="datepicker">
            </label>

            <label>
                <?= _("Ende Bearbeitungszeitraum der Admins") ?>
                <input type="text" name="data[adminedit_end]" value="<?= $profile['adminedit_end'] ? date("d.m.Y H:i", $profile['adminedit_end']) : "" ?>" class="datepicker">
            </label>
        <? endif ?>

        <label>
            <?= _("Standardfragebogen (nur aktive werden angezeigt)") ?>
            <select name="data[form_id]" class="select2">
                <option value=""></option>
                <? foreach (EvasysForm::findBySQL("active = '1' ORDER BY name ASC") as $form) : ?>
                    <option value="<?= htmlReady($form->getId()) ?>"<?= $form->getId() == $profile['form_id'] ? " selected" : "" ?> title="<?= htmlReady($form['description']) ?>">
                        <?= htmlReady($form['name']) ?>:
                        <?= htmlReady($form['description']) ?>
                    </option>
                <? endforeach ?>
            </select>
        </label>

        <label>
            <?= _("Art der Evaluation") ?>
            <select name="data[mode]">
                <option value=""></option>
                <option value="paper"<?= $profile['mode'] === "paper" ? " selected" : "" ?>>
                    <?= _("Papierbasierte Evaluation") ?>
                </option>
                <option value="online"<?= $profile['mode'] === "online" ? " selected" : "" ?>>
                    <?= _("Online-Evaluation") ?>
                </option>
            </select>
        </label>

        <label>
            <?= _("Adresse für den Versand der Fragebögen") ?>
            <textarea name="data[address]"><?= htmlReady($profile['address']) ?></textarea>
        </label>

        <? if (is_a($profile, "EvasysInstituteProfile")) : ?>
        <label>
            <?= _("Weitere Emails, an die die Ergebnisse gesendet werden sollen (mit Leerzeichen getrennt)") ?>
            <input type="text" name="data[results_email]" value="<?= htmlReady($profile['results_email']) ?>">
        </label>
        <? endif ?>

    </fieldset>

    <fieldset class="forms_for_types">
        <legend>
            <?= _("Standardfragebögen nach Veranstaltungstypen") ?>
        </legend>

        <table class="default">
            <tbody>
                <? foreach (SemType::getTypes() as $sem_type) : ?>
                <tr>
                    <td>
                        <?= htmlReady($GLOBALS['SEM_CLASS'][$sem_type['class']]['name']) ?>: <?= htmlReady($sem_type['name']) ?>
                    </td>
                    <td>
                        <label>
                            <div>
                                <?= _("Standardfragebogen") ?>
                            </div>
                            <select name="forms_by_type[<?= htmlReady($sem_type['id']) ?>]" class="select2">
                                <option value=""></option>
                                <? foreach (EvasysForm::findBySQL("active = '1' ORDER BY name ASC") as $form) : ?>
                                    <option value="<?= htmlReady($form->getId()) ?>"<?= $forms_by_type[$sem_type['id']][0] == $form->getId() ? " selected" : "" ?>  title="<?= htmlReady($form['description']) ?>">
                                        <?= htmlReady($form['name']) ?>:
                                        <?= htmlReady($form['description']) ?>
                                    </option>
                                <? endforeach ?>
                            </select>
                        </label>

                        <label>
                            <div>
                                <?= _("Verfügbar") ?>
                            </div>
                            <select name="available_forms_by_type[<?= htmlReady($sem_type['id']) ?>][]" multiple class="select2">
                                <option value=""></option>
                                <? foreach (EvasysForm::findBySQL("active = '1' ORDER BY name ASC") as $form) : ?>
                                    <option value="<?= htmlReady($form->getId()) ?>"<?= in_array($form->getId(), (array) $available_forms_by_type[$sem_type['id']]) ? " selected" : "" ?>  title="<?= htmlReady($form['name'].": ".$form['description']) ?>">
                                        <?= htmlReady($form['name']) ?>
                                    </option>
                                <? endforeach ?>
                            </select>
                        </label>
                    </td>
                    <td>
                        <? if (count($available_forms_by_type[$sem_type['id']])) : ?>
                        <a href="<?= PluginEngine::getLink($plugin, array(), "forms/sort/".$this->controller->profile_type."/".$sem_type['id']."/".$profile->getId()) ?>"
                           title="<?= _("Sortierung bearbeiten") ?>"
                           data-dialog>
                            <?= Icon::create("settings", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
                        </a>
                        <? endif ?>
                    </td>
                </tr>
                <? endforeach ?>
            </tbody>
        </table>
    </fieldset>

    <fieldset>
        <legend><?= ucfirst(EvasysMatching::wording("freiwillige Evaluationen")) ?></legend>

        <label>
            <?= _("Beginn der Antragsfrist") ?>
            <input type="text" name="data[antrag_begin]" value="<?= $profile['antrag_begin'] ? date("d.m.Y H:i", $profile['antrag_begin']) : "" ?>" class="datepicker">
        </label>
        <? if ($this->controller->profile_type === "institute") : ?>
            <? $default_value = $profile->getParentsDefaultValue("antrag_begin") ?>
            <span title="<?= _("Standardwert, wenn nichts eingetragen ist.") ?>"
                  class="default_value">(<?= $default_value ? date("d.m.Y H:i", $default_value) : _("Kein Standardwert") ?>)</span>
        <? endif ?>

        <label>
            <?= _("Ende der Antragsfrist") ?>
            <input type="text" name="data[antrag_end]" value="<?= $profile['antrag_end'] ? date("d.m.Y H:i", $profile['antrag_end']) : "" ?>" class="datepicker">
        </label>
        <? if ($this->controller->profile_type === "institute") : ?>
            <? $default_value = $profile->getParentsDefaultValue("antrag_end") ?>
            <span title="<?= _("Standardwert, wenn nichts eingetragen ist.") ?>"
                  class="default_value">(<?= $default_value ? date("d.m.Y H:i", $default_value) : _("Kein Standardwert") ?>)</span>
        <? endif ?>

        <label>
            <?= _("Informationstext") ?>
            <textarea name="data[antrag_info]"><?= htmlReady($profile['antrag_info']) ?></textarea>
        </label>

    </fieldset>

    <script>
        jQuery(function () {
            jQuery("input.datepicker").datetimepicker();
            jQuery(".forms_for_types .select2").select2({
                "closeOnSelect": false,
                "width": 'resolve'
            });
        });
    </script>
    <style>
        .ui-widget, #layout_wrapper #barBottomContainer {
            z-index: 100000 !important;
        }
    </style>

    <div style="text-align: center;">
        <?= \Studip\Button::create(_("Speichern")) ?>
    </div>

</form>

<? else : ?>
    <?= MessageBox::info(sprintf(_("Wählen Sie erst eine %s aus."), EvasysMatching::wording("Einrichtung"))) ?>
<? endif ?>

<?
if ($this->controller->profile_type === "institute") {
    $list = new SelectWidget(
        _('Einrichtung'),
        PluginEngine::getURL($plugin, array(), "instituteprofile/change_institute"),
        'institute'
    );
    $insts = Institute::getMyInstitutes($GLOBALS['user']->id);
    $list->class = 'institute-list';
    if ($GLOBALS['perm']->have_perm('root') || (count($insts) > 1)) {
        $list->addElement(new SelectElement(
            'all',
            $GLOBALS['perm']->have_perm('root') ? _('Alle') : _('Alle meine Einrichtungen'),
            $GLOBALS['user']->cfg->MY_INSTITUTES_DEFAULT === 'all'),
            'select-all'
        );
    }

    foreach ($insts as $institut) {
        $list->addElement(
            new SelectElement(
                $institut['Institut_id'],
                (!$institut['is_fak'] ? ' ' : '') . $institut['Name'],
                $GLOBALS['user']->cfg->MY_INSTITUTES_DEFAULT === $institut['Institut_id']
            ),
            'select-' . $institut['Institut_id']
        );
    }
    Sidebar::Get()->addWidget($list, 'filter_institute');
}