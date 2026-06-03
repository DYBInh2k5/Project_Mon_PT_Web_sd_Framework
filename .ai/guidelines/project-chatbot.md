# Project Chatbot Guideline

This Laravel 12 admin project uses a support chatbot for sales and order operations.

When working on the chatbot:

- Prefer order lookup from the local database before falling back to Gemini.
- Keep the response shape stable: `message` plus up to 3 `suggestions`.
- Never expose `GEMINI_API_KEY` in views, logs, or docs.
- Use `CustomerSupportChatbot` as the orchestration layer.
- Use `GeminiChatService` only as the external AI fallback.
- Keep the floating chatbot entry point small and fixed in the bottom-right corner.
- Update `docs/09-ORDERS-CHATBOT-PAYMENT.md` and `docs/11-FULL-PROJECT-GUIDE.md` when chatbot behavior changes.
