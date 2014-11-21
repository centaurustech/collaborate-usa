    		</div>
            </td>
    	</tr>

        <?php /*
        <tr><td colspan="4">
        <div style="height:10px; background:#ffffff;">
        </div></td></tr>
        */ ?>

    </table>
    </div>

    <?php
    $copy_rights = date('Y').' collaborateusa.com | Administration Section';
    ?>

    <?php if(!isset($no_header)){ ?>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="92%" id="FOOTER" class="no_print">
    <tr><td>
        <a href="<?php echo DOC_ROOT_ADMIN; ?>" style="font-size:12px;">&copy; <?php echo $copy_rights; ?></a>
        <br />
        </td></tr>
    </table>
    <?php } ?>
    <br /><br />

</div></center>
</body>
</html>