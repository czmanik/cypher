from playwright.sync_api import sync_playwright

def test_footer(page):
    page.goto("http://localhost:8081")
    # Wait for footer to be visible
    page.wait_for_selector("footer")

    # Scroll to footer
    footer = page.locator("footer")
    footer.scroll_into_view_if_needed()

    # Screenshot footer
    footer.screenshot(path="verification_footer.png")
    print("Screenshot saved to verification_footer.png")

if __name__ == "__main__":
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        try:
            test_footer(page)
        except Exception as e:
            print(f"Error: {e}")
        finally:
            browser.close()
