# 🧠 NeuronMind

**NeuronMind** is a fully working example that demonstrates how to build an **agentic AI assistant in PHP**, powered by [**Neuron-AI**](https://github.com/inspector-apm/neuron-ai), the open-source PHP framework for building LLM-based agents.

> ⚠️ This is not a production-ready product, but an **exploratory project** to showcase how to structure intelligent agent workflows using tools, reasoning, and web search — entirely in PHP.

The architecture is inspired by [Google's gemini-fullstack-langgraph-quickstart](https://github.com/google-gemini/gemini-fullstack-langgraph-quickstart), and reimagined using [**Neuron-AI**](https://github.com/inspector-apm/neuron-ai) and [Neuron-Graph](https://codeberg.org/sixty-nine/neuron-graph).

---

## 🚀 Why Neuron-AI?

**Neuron-AI** brings agentic reasoning, tool calling, chat history, RAG, and integration with LLM providers (OpenAI, Ollama...) — all in native PHP.

This project shows:

- How to build a tool-augmented conversational assistant
- How to decide when to search or answer
- How to compose reasoning steps using graph nodes
- How to stay fully in PHP with no external runtimes

---

## 🧩 What this example implements

This project defines a **search tool** using a graph-based reasoning workflow. When a user asks a question, the agent decides whether to answer directly or invoke the tool.

### Tool Workflow

```
User input
   ↓
QueryWriterNode     → generates refined search queries
   ↓
SearcherNode        → performs web search (via Jina)
   ↓
ReflectionNode      → evaluates if results are sufficient
                      ↳ if not, suggests follow-up queries and loops back to SearcherNode
   ↓
AnswerNode          → generates final response
```

All of this is wrapped in a `Tool` called `search`, registered inside the agent.

---

## 🧠 Features

- ✅ Agent chat powered by `neuron-ai`
- 🔁 Multi-step reasoning with `neuron-graph`
- 🔍 Web search via Jina
- 🛠️ Tool integration and triggering
- 💬 Small talk handled directly

---

## ⚙️ Requirements

- PHP 8.2+
- Composer
- OpenAI API key
- Jina API key (for search)

---

## 🛠️ Installation

```bash
git clone https://github.com/asterixcapri/neuron-mind.git
cd neuron-mind
composer install
cp .env.example .env
# Add your OPENAI_API_KEY and JINA_API_KEY to .env
```

---

## ▶️ Start Chatting

```bash
php bin/console.php chat
```

Example:

```text
php@c1c36fd26e1b:/app$ ./bin/console.php chat
NeuronMind CLI - type 'exit' to quit
You> hello
NeuronMind> Hello! How can I assist you today?
You> compare electric cars under 50000 euro now in 2025
QueryWriterNode - Starting query writer
QueryWriterNode - Query writer response: {
    "rationale": "These queries target information on electric cars available in 2025 under a specific price point, focusing on comparison aspects, ensuring the user can find relevant models and their attributes.",
    "queries": [
        "2025 electric cars under 50000 euro comparison",
        "best electric vehicles 2025 price under 50000 euro",
        "2025 electric car models reviews under 50000 euro"
    ]
}

SearcherNode - Starting search
SearcherNode - Query: 2025 electric cars under 50000 euro comparison
SearcherNode - Search result: Here’s a summary of key findings from various sources regarding electric cars ...
SearcherNode - Query: best electric vehicles 2025 price under 50000 euro
SearcherNode - Search result: Here’s a summary of the best electric vehicles (EVs) priced under €50,000 fo...
SearcherNode - Query: 2025 electric car models reviews under 50000 euro
SearcherNode - Search result: Here’s a summary of reviews and key information about electric car models unde...
ReflectionNode - User query: electric cars under 50000 euro 2025 comparison
ReflectionNode - Search results: ["Here\u2019s a summary of key findings from various so...","Here\u2019s a summary of the best electric vehicles (...","Here\u2019s a summary of reviews and key information ..."]
ReflectionNode - Reflection response: { "is_sufficient": true }

AnswerNode - Original question: electric cars under 50000 euro 2025 comparison
AnswerNode - Search results: ["Here\u2019s a summary of key findings from various so...","Here\u2019s a summary of the best electric vehicles (...","Here\u2019s a summary of reviews and key information ..."]
AnswerNode - Answer: If you're considering electric cars under €50,000 for 2025, you have several e...
NeuronMind> Here’s a comparison of some of the best electric cars available under €50,000 in 2025:

### 1. **Renault 5 E-Tech**
   - **Price**: Affordable and stylish.
   - **Features**: Modern technology, compact design ideal for city driving.
   - **Pros**: Great value for money, appealing aesthetics.

### 2. **Tesla Model 3**
   - **Price**: Near €50,000 (varies by configuration).
   - **Features**: Remarkable performance, impressive range, and robust safety features.
   - **Pros**: Excellent driving experience, great family car.

### 3. **Hyundai Ioniq 5**
   - **Price**: Competitive pricing.
   - **Features**: Spacious interior, cutting-edge technology, and fast charging.
   - **Pros**: Good balance of comfort, style, and efficiency.

### Community Insights
Many buyers favor the **Chevy Bolt** and **Hyundai Kona Electric** for their value and enjoyable driving experiences. It's essential to consider your preferences, like range and features, to make an informed decision.

### Conclusion
The electric vehicle market offers a diverse selection of models under €50,000 in 2025, providing great value and technology. Be sure to explore user reviews and specific comparisons to find the best fit for your needs. If you have any specific questions or need further assistance, feel free to ask!
You> very helpful, thanks
NeuronMind> You're welcome! I'm glad you found the information helpful. If you have any more questions or need assistance with anything else, just let me know!
You> exit
```

NeuronMind will rewrite the query, perform a web search, reflect, and generate a detailed answer.

---

## 🧱 Built With

- [Neuron-AI](https://github.com/inspector-apm/neuron-ai) – Agent framework in PHP
- [Neuron-Graph](https://codeberg.org/sixty-nine/neuron-graph) – Workflow graph engine
- [Jina AI](https://jina.ai) – Web search API

---

## 🧠 Inspiration

This project is heavily inspired by:

**[🌐 gemini-fullstack-langgraph-quickstart](https://github.com/google-gemini/gemini-fullstack-langgraph-quickstart)**  
Implemented from scratch in **pure PHP** using Neuron-AI tools.

---

## 📄 License

MIT License

---

Made by [@asterixcapri](https://github.com/asterixcapri)
