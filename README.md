![NBCC_Horizontal_White](https://github.com/user-attachments/assets/eba96a49-1096-4679-9f11-2ca1d964e42e)

## NBCC IDL Practicum
<p> This is a git repo for the IDL project made with:</p>
<ul>
  <li>Laravel & Livewire</li>
  <li>Blade, JQuery, Tailwind CSS</li>
  <li>PostGreSQL</li>
</ul>

<table>
  <tr>
    <td>
      <img src="https://github.com/user-attachments/assets/9f961054-7f58-4fde-8cf5-ff10ddb6589a" alt="laravel" width="200px">
    </td>
    <td>
      <img src="https://github.com/user-attachments/assets/6dda5685-d1d6-4ef3-b90a-9c956c1cdf4c" alt="livewire" width="200px">
    </td>
    <td>
      <img src="https://github.com/user-attachments/assets/5eefcbbb-34e8-4b5c-9676-3523cd468b62" alt="Postgres" width="200px">
    </td>
  </tr>
</table>

## Getting Started
<p>The first step is to make sure that you have installed
  <a href="https://herd.laravel.com/windows">laravel herd</a>.
</p>
<p>Once you have done that download the repositorys zip and navigate to where you will be creating the laravel project. For example with herd your project should be placed somewhere similar to </p>

```markdown
C:\Users\[YOUR USER NAME]\Herd\
```

<p>once you are there run the following commands in CMD or powershell to set up a laravel application. Make sure before running the commands you are in the projects directory so \Herd\[NAME OF PROJECT]</p>

```markdown
composer install
copy .env.example .env
npm install
php artisan key:generate
npm audit fix (if needed)
```

<p>now before launching the application verify all the information in the .env file is correct. mainly the database connection information. Once that is done feel free to run the project by using</p>

```markdown
npm run dev
```

