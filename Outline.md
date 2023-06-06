# PHP-Scatterplot

## Functions

### Class:Scatterplot extends Plotter
- data
- layers
- matrix: future version

### Class:Plotter extends Analyzer
- plot
- regressionLine
- regressionCurve
- referenceLine
- specificationLimit
- create($path)

### Class:Analyzer
- mean($data)
- variance($data)
- covariance($dataX, $dataY)
- standardDeviation($data)
- correlationCoefficient($dataX, $dataY)
- regressionLineFormula($dataX, $dataY)
- regressionCurveFormula: future version
- getUcl($data)
- getLcl($data)
- outliers($data)
