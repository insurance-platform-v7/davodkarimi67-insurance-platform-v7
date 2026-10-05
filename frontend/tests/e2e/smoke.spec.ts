import { test, expect } from "@playwright/test";

test("frontend smoke test", async ({ page }) => {
  await page.goto("/");
  await expect(page).toHaveTitle(/.+/);
});