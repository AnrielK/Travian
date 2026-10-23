<?php
// Options → Graphic pack: choose between the T4.4 look and the older Travian 4.0 packs (see application/gpack.php).
$current = gpack_current();
?>
<style>
div.options #gpackList { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 0 10px; }
div.options #gpackList td { white-space: normal; vertical-align: top; padding: 6px; border: 1px solid #c0c0c0; background: #fff; overflow-wrap: break-word; }
div.options #gpackList td.sel { width: 24px; text-align: center; vertical-align: middle; border-right: 0; }
div.options #gpackList td.info { border-left: 0; border-right: 0; }
div.options #gpackList td.prev { width: 170px; border-left: 0; text-align: right; }
div.options #gpackList td.prev img { width: 160px; height: 100px; border: 1px solid #999; display: inline-block; }
div.options #gpackList tr.active td { background: #f1f8e0; border-color: #71d000; }
div.options #gpackList label { display: block; cursor: pointer; }
div.options #gpackList .name { font-weight: bold; font-size: 14px; }
div.options #gpackList .desc { color: #666; margin-top: 4px; white-space: normal; }
div.options #gpackList .current { color: #71d000; font-weight: bold; margin-top: 6px; }
div.options p.gpackNote { color: #666; font-size: 11px; }
</style>
<h4 class="round spacer">Graphic pack</h4>
<p>Choose how the game looks. The setting is saved in this browser only, so the second player window can use a different look.</p>
<form action="options.php?s=3" method="post">
    <table id="gpackList" class="transparent" cellpadding="0" cellspacing="0">
        <tbody>
        <?php foreach (gpack_list() as $id => $gp): ?>
            <tr class="<?php echo $id === $current ? 'active' : ''; ?>">
                <td class="sel"><input class="radio" type="radio" name="gpack" id="gp_<?php echo $id; ?>" value="<?php echo $id; ?>" <?php echo $id === $current ? 'checked="checked"' : ''; ?> /></td>
                <td class="info">
                    <label for="gp_<?php echo $id; ?>">
                        <div class="name"><?php echo htmlspecialchars($gp['name']); ?></div>
                        <div class="desc"><?php echo htmlspecialchars($gp['desc']); ?></div>
                        <?php if ($id === $current): ?><div class="current">✓ active</div><?php endif; ?>
                    </label>
                </td>
                <td class="prev"><label for="gp_<?php echo $id; ?>"><img src="<?php echo $gp['preview']; ?>" alt="" /></label></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="submitButtonContainer">
        <button type="submit" value="save" name="s1" id="btn_ok" class="green">
            <div class="button-container addHoverClick"><div class="button-background"><div class="buttonStart"><div class="buttonEnd"><div class="buttonMiddle"></div></div></div></div>
                <div class="button-content">Save</div></div>
        </button>
    </div>
</form>
<p class="gpackNote">The classic packs re-skin the T4.4 layout with the original Travian 4.0 graphics
(gpack <i>travian_Travian_4.0_41</i> and <i>travian_basic</i>). Game screens and functions stay the same.</p>
