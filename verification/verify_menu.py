from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    # Use iPhone 12 viewport
    context = browser.new_context(viewport={"width": 390, "height": 844})
    page = context.new_page()

    # 1. Load page
    page.goto("http://127.0.0.1:8000")

    # Debug: screenshot the initial state
    page.screenshot(path="verification/debug_initial.png")

    # 2. Open menu
    # The hamburger button is visible on mobile.
    # It has text "☰" when closed.
    # Try using a selector instead of role/name if name is tricky
    menu_button = page.locator("button.md\\:hidden")

    # Check if button is visible
    if not menu_button.is_visible():
        print("Menu button not visible!")
        print(page.content())
        return

    menu_button.click()

    # Wait for menu to appear
    mobile_menu = page.locator(".md\\:hidden.absolute.top-16")
    expect(mobile_menu).to_be_visible()

    page.screenshot(path="verification/1_menu_open.png")
    print("Menu opened.")

    # 3. Close by clicking X
    # The button text changes to ✕ when open
    # We can reuse the menu_button locator because it's the same button
    menu_button.click()

    expect(mobile_menu).not_to_be_visible()
    page.screenshot(path="verification/2_menu_closed_by_x.png")
    print("Menu closed by X.")

    # 4. Re-open and close by clicking outside
    menu_button.click()
    expect(mobile_menu).to_be_visible()

    # Click somewhere else. The menu is top-16 (64px) + height.
    # Let's click at the bottom of the screen.
    page.mouse.click(200, 600)

    expect(mobile_menu).not_to_be_visible()
    page.screenshot(path="verification/3_menu_closed_by_outside.png")
    print("Menu closed by clicking outside.")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
