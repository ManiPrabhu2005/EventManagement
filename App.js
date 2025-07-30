/* import logo from './logo.svg';*/
import M from './M.jpg';
import './App.css';

function App() {

  function handlenamecheck(){
      const names=['Mani','Prabhu','Prasath','Dhiwa','Pradeep']
      const int=Math.floor(Math.random()*5)
      return names[int]
  }
  return (
    <div className="App">
      <header className="App-header">
        
        <img src={M} className="App-logo" alt="logo" />
        <p>
          🙏 Welcome to our website {handlenamecheck()} thank you ❣️
        </p>
        <a
          className="App-link"
          href="https://reactjs.org"
          target="_blank"
          rel="noopener noreferrer"
        >
          Learn React
        </a>
      </header>
    </div>
  );
}

export default App;
