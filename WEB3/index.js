import 'dotenv/config';
import { GoogleGenAI } from '@google/genai';
import readline from 'readline/promises';
import { stdin as input, stdout as output } from 'process';

// Configuration
const MODEL_NAME = process.env.GEMINI_MODEL || 'gemini-2.5-flash';
const apiKey = process.env.GEMINI_API_KEY;

if (!apiKey) {
  throw new Error(
    'GEMINI_API_KEY environment variable is not set. Please check your .env file.'
  );
}

// Initialize the client
const ai = new GoogleGenAI({ apiKey });

// Helper to create a new chat session (used for reset)
function createChat(systemInstruction) {
  return ai.chats.create({
    model: MODEL_NAME,
    config: {
      systemInstruction: systemInstruction ||
        'You are a helpful assistant that helps generate code, explain concepts, and help build web backends and frontends for practice and later commercial use.',
    },
  });
}

let chat = createChat();

async function main() {
  const rl = readline.createInterface({ input, output, terminal: true });

  console.log('Gemini interactive CLI');
  console.log('-------------------------------------');
  console.log(`Model: ${MODEL_NAME}`);
  console.log('Type a message and press Enter to send.');
  console.log('Commands: /exit (quit), /reset (new chat session), /help (show this help)');
  console.log('-------------------------------------');

  while (true) {
    try {
      const userText = (await rl.question('You: ')).trim();

      if (!userText) continue;

      if (userText === '/exit') {
        console.log('Goodbye!');
        rl.close();
        break;
      }

      if (userText === '/help') {
        console.log('Commands:');
        console.log('  /exit  - quit');
        console.log('  /reset - create a fresh chat session (clears history)');
        console.log('  /help  - show this help');
        continue;
      }

      if (userText === '/reset') {
        chat = createChat();
        console.log('Chat reset. New session started.');
        continue;
      }

      // Send the message to the model (multi-turn chat)
      const response = await chat.sendMessage({ message: userText });

      // `response.text` contains the generated text in the JS SDK
      console.log('\nGemini:', response.text, '\n');
    } catch (err) {
      console.error('Error communicating with Gemini:', err?.message || err);
      // If API key/auth issues are the cause, hint to the user
      if (err && err.status === 401) {
        console.error('Authentication failed (401). Check your API key and billing/account status.');
      }
    }
  }
}

main().catch((err) => {
  console.error('Fatal error:', err);
  process.exit(1);
});
