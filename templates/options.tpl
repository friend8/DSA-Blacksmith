{include file="header.tpl"}
<div id="options">
    <form method="post" action="index.php?page=Options">
        {if $errorGeneral}
            <div class="error">{$translator->gt($errorGeneral)}</div>
        {/if}
        {if $messageGeneral}
            <div class="message">{$translator->gt($messageGeneral)}</div>
        {/if}
        <table>
            <tbody>
                <tr>
                    <td>{$translator->gt('username')}:</td>
                    <td>
                        <input type="text" name="username" value="{$user->getName()}" />
                    </td>
                </tr>
                <tr>
                    <td>{$translator->gt('email')}:</td>
                    <td>
                        <input type="text" name="email" value="{$user->getEmail()}" />
                    </td>
                </tr>
                <tr>
                    <td>{$translator->gt('showPublicBlueprints')}:</td>
                    <td>
                        <input type="checkbox" name="showPublicBlueprints" value="1" {if ($user->getShowPublicBlueprints())}checked="checked"{/if} />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {add_form_salt formName="generalOptions"}
                        <input type="submit" value="{$translator->gt('change')}"
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <form method="post" action="index.php?page=Options">
        {if $errorPassword}
            <div class="error">{$errorPassword}</div>
        {/if}
        {if $messagePassword}
            <div class="message">{$messagePassword}</div>
        {/if}
        <table>
            <tbody>
                <tr>
                    <td>{$translator->gt('password')}:</td>
                    <td>
                        <input type="password" name="password" />
                    </td>
                </tr>
                <tr>
                    <td>{$translator->gt('repeatPassword')}:</td>
                    <td>
                        <input type="password" name="repeatPassword" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {add_form_salt formName="passwordOptions"}
                        <input type="submit" value="{$translator->gt('change')}" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
{include file="footer.tpl"}