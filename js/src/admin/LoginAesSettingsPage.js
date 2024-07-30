import app from 'flarum/common/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Select from 'flarum/common/components/Select';
import Switch from 'flarum/common/components/Switch'; 
 
const settingsPrefix = 'api_login.';
const localePrefix = 'api_login.admin.settings.';

export default class LoginAesSettingsPage extends ExtensionPage {
  oninit(vnode) {
    console.log('LoginAesSettingsPage oninit');
    super.oninit(vnode);
  } 
  content() {
    console.log('LoginAesSettingsPage content');
    return [
      m('.container', [
        m('.LoginAesSetting', [
          m('.Form-group', [
            m('label', app.translator.trans(localePrefix + 'aes_key')),
            m('input[type=text].FormControl', {
              bidi: this.setting(settingsPrefix + 'aes_key', ''),
              placeholder: '2',
              style: 'width:25%',
            }),
          ]), 
          m('.Form-group', [
            m('label', app.translator.trans(localePrefix + 'aes_iv')),
            m('input[type=text].FormControl', {
              bidi: this.setting(settingsPrefix + 'aes_iv', ''),
              placeholder: '',
              style: 'width:25%',
            }),
          ]), 
          m('.Form-group', [
            m('label', app.translator.trans(localePrefix + 'rand')),
            m('input[type=text].FormControl', {
              bidi: this.setting(settingsPrefix + 'rand', ''),
              placeholder: '',
              style: 'width:25%',
            }),
          ]), 
          this.submitButton(),
        ]),
      ]),
    ];
  }
}
