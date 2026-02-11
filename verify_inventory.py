import os
import sys
from playwright.sync_api import sync_playwright

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(viewport={'width': 1920, 'height': 1080})
        page = context.new_page()

        # 1. Login
        print("Logging in...")
        page.goto("http://127.0.0.1:8000/admin/login")
        page.fill('input[type="email"]', "admin@cypher93.cz")
        page.fill('input[type="password"]', "password")
        page.click('button[type="submit"]')
        page.wait_for_load_state('networkidle')

        # 2. Visit Fast Write-off
        print("Visiting Fast Write-off...")
        page.goto("http://127.0.0.1:8000/admin/fast-write-off")
        page.wait_for_load_state('networkidle')
        # Wait for some content to ensure it loaded
        page.wait_for_selector('h3', timeout=5000)
        page.screenshot(path="fast_write_off.png")
        print("Screenshot saved: fast_write_off.png")

        # 3. Visit Inventory Items
        print("Visiting Inventory Items...")
        page.goto("http://127.0.0.1:8000/admin/inventory-items")
        page.wait_for_load_state('networkidle')
        page.screenshot(path="inventory_items.png")
        print("Screenshot saved: inventory_items.png")

        browser.close()

if __name__ == "__main__":
    run()
