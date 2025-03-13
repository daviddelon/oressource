import { test, expect } from '@playwright/test';
import { faker } from '@faker-js/faker';

test('test', async ({ page }) => {
  await page.goto('http://localhost:3000/ifaces/login.html');
  await page.getByRole('textbox', { name: 'Mail :' }).click();
  await page.getByRole('textbox', { name: 'Mail :' }).fill('admin@oressource.org');
  await page.getByRole('textbox', { name: 'Mail :' }).press('Tab');
  await page.getByRole('textbox', { name: 'Mot de passe :=' }).fill('admin');
  await page.getByRole('textbox', { name: 'Mot de passe :=' }).press('Enter');
  await page.getByRole('link', { name: 'Points de vente' }).click();
  await page.locator('[href*="../ifaces/ventes.php?numero=1"]').click();
  await page.getByRole('button', { name: 'consequatur' }).click();
  await page.getByRole('textbox', { name: 'Prix unitaire:' }).click();
  await page.getByRole('textbox', { name: 'Prix unitaire:' }).fill(faker.string.numeric({length: 1,exclude: ['0']}));
  await page.getByRole('textbox', { name: 'Prix unitaire:' }).press('Tab');
  await page.getByRole('textbox', { name: 'Masse unitaire:' }).fill(faker.string.numeric({length: 1,exclude: ['0']}));
  await page.getByRole('button', { name: 'Ajouter' }).click();
  await page.getByRole('button', { name: 'Encaisser' }).click();
});