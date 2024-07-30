import app from 'flarum/admin/app';
import LoginAesSettingsPage from './LoginAesSettingsPage';
 
app.initializers.add('thefunpower-api-login', () => {  
  app.extensionData
    .for('thefunpower-api-login')
    .registerPage(LoginAesSettingsPage);
});

